<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = ["name", "track", "type", "slug", "defaulttime", "public", "rank"];

    protected $_all_results = null;
    protected $_families = null;

    protected $_constraints = null;
    protected $casts = [
        'int' => 'integer',
    ];

    public function fullname(): string
    {
        return $this->name . " " . $this->track;
    }


    public function benchmarks()
    {
        return $this->hasMany("App\Models\Benchmark", 'evaluation_id');
    }


    public function solvers(): BelongsToMany
    {
        return $this->belongsToMany(Solver::class, "evaluation_solver", "evaluation_id", "solver_id")->withPivot("nb_benchmarks");
    }


    public function initResults($solvers)
    {
        if ($this->type == "cop")
            $this->initBestBounds($solvers);
        else $this->initSAT($solvers);
    }

    public function initSAT($solvers)
    {
        foreach ($this->benchmarks as $benchmark) {
            $nbSAT = 0;
            $nbUNSAT = 0;
            foreach ($solvers as $solver_id) {
                $result = Result::where("benchmark_id", $benchmark->id)->where("solver_id", $solver_id)->first();

                if ($result == false || $result->bug || $result->unsupported)
                    continue;
                if ($result->status == "SAT")
                    $nbSAT++;
                if ($result->status == "UNSAT")
                    $nbUNSAT++;

            }
            if ($nbSAT > 0 && $nbUNSAT > 0)
                $benchmark->status = "UNKNOWN";
            else {
                if ($nbSAT > 0)
                    $benchmark->status = "SAT";
                else
                    $benchmark->status = "UNSAT";
            }
            $benchmark->save();
        }
    }

    public function initBestBounds($solvers)
    {
        foreach ($this->benchmarks as $benchmark) {
            if (str_contains(strtoupper($benchmark->type), "MAX"))
                $type = "MAXIMIZE";
            else
                $type = "MINIMIZE";
            $benchmark->best_bound = null;
            $benchmark->status = "UNKNOWN";
            $best_bound = $this->best_bound_cop($benchmark->id, $type, $solvers, $this->defaulttime);
            $benchmark->best_bound = $best_bound->bound;

            if ($best_bound->unsat)
                $benchmark->status = "UNSAT";
            if ($best_bound->optimum)
                $benchmark->status = "OPTIMUM";
            else if ($best_bound->bound != null)
                $benchmark->status = "SAT";
            $benchmark->save();
        }
    }

    public function all_results()
    {
        if ($this->_all_results != null)
            return $this->_all_results;
        $this->_all_results = [];
        foreach ($this->solvers as $solver) {
            $tmp = $solver->results($this);
            foreach ($tmp as $data) {
                $data->time = round($data->time);
                $this->_all_results[$data->solver_id][$data->benchmark_id] = $data;
            }
        }
        return $this->_all_results;
    }

    public function all_results_cop($time_limit)
    {
        if ($this->_all_results != null)
            return $this->_all_results;
        $this->all_results();
        foreach ($this->_all_results as $data_solver) {
            foreach ($data_solver as $data) {

                $json = str_replace("'", '"', $data->bounds);
                $bounds = json_decode($json);
                $data->bound = null;
                foreach ($bounds as $b)
                    if ($b->time <= $time_limit) {
                        $data->bound = $b->bound;
                        $data->bound_time = $b->time;
                    }
                unset($data->bounds);
                //if ($data->benchmark_id == 701)
                //    dd($data);
            }
        }
        return $this->_all_results;
    }


    public function families()
    {
        if ($this->_families != null)
            return $this->_families;
        $this->_families = DB::table('benchmarks')
            ->where('evaluation_id', $this->id)
            ->distinct()
            ->pluck('family')
            ->toArray();
        return $this->_families;
    }

    public function constraints()
    {
        if ($this->_constraints != null)
            return $this->_constraints;
        $tmp = [];
        foreach ($this->benchmarks as $benchmark) {
            preg_match_all('/#(\w+):/', $benchmark->info_constraints, $matches);
            $tmp = array_merge($tmp, $matches[1]);
        }
        $this->_constraints = array_values(array_unique($tmp));
        return $this->_constraints;
    }

    public function get_type()
    {
        if ($this->type == "sat" || $this->type == "csp")
            return "sat";
        return "cop";
    }


    public function best_bound_cop($benchmark_id, $type, $selected_solvers, $time_limit)
    {
        $all_results = $this->all_results_cop($time_limit);
        $tmp = (object)["bound" => null, "optimum" => false, "unsat" => false];

        foreach ($selected_solvers as $solver_id) {
            $data = $all_results[$solver_id][$benchmark_id];
            if ($data->bug)
                continue;
            if ($data->status == "UNSAT") {
                $tmp->unsat = true;
                return $tmp;
            }
            if ($data->bound === null) continue;
            if ($tmp->bound === null || ($type == "MAXIMIZE" && $tmp->bound < $data->bound) || ($type == "MINIMIZE" && $tmp->bound > $data->bound))
                $tmp->bound = $data->bound;
            if ($data->time != -1 && $data->time <= $time_limit)
                $tmp->optimum = true;
        }
        return $tmp;
    }

    public function score_cop($benchmark_id, $solver_id, $type, $best_bound, $time_limit)
    {
        $tmp = (object)["optimum" => 0, "bb1" => 0, "bb2" => 0];
        $all_results = $this->all_results_cop($time_limit);
        $data = $all_results[$solver_id][$benchmark_id];
        if (($type == "MAXIMIZE" && $best_bound->bound > $data->bound) || ($type == "MINIMIZE" && $best_bound->bound < $data->bound))
            return $tmp;
        if ($data->bound === null)
            return $tmp;

        if ($data->time != -1 && $data->time <= $time_limit) {
            $tmp->optimum = 1;
            return $tmp;
        }
        if ($best_bound->optimum == false)
            $tmp->bb1++;
        else $tmp->bb2++;
        return $tmp;
    }
}






