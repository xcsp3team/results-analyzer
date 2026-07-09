<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = ["name", "track", "type", "slug", "defaulttime", "public"];

    protected $casts = [
        'int' => 'integer',
    ];

    public function fullname(): string {
        return $this->name . " " . $this->track;
    }

    public function solvers()
    {
        if ($this->type == "cop")
            return DB::select("select distinct solvers.* from solvers " .
                "inner join results_cop on solvers.id=solver_id " .
                "inner join benchmarks_cop on benchmarks_cop.id=benchmark_id " .
                "inner join competitions on competition_id=competitions.id " .
                "where competitions.id= ? order by solvers.name, solvers.version",
                [$this->id]);

        return DB::select("select distinct solvers.* from solvers " .
            'inner join results on solvers.id=solver_id ' .
            "inner join benchmarks on benchmarks.id=benchmark_id " .
            "inner join competitions on competition_id=competitions.id " .
            "where competitions.id= ? order by solvers.name, solvers.version", [$this->id]);
    }


    public function benchmarks()
    {
        if ($this->type == "cop")
            return $this->hasMany(Benchmark_cop::class, 'competition_id');

        return $this->hasMany("App\Models\Benchmark", 'competition_id');
    }


    // Used with Filament Table

    public function benchmarks2() : HasMany{
        return $this->hasMany(Benchmark::class); // table "benchmarks"
    }

    public function benchmarksCop(): HasMany   // For Filament
    {
        return $this->hasMany(Benchmark_cop::class); // table "benchmarks_cop"
    }

    public function solvers2(): BelongsToMany {
        return $this->belongsToMany(Solver::class, "competition_solver", "competition_id", "solver_id")->withPivot("nb_benchmarks");
    }
    public function solversCop(): BelongsToMany {
        return $this->belongsToMany(Solver::class, "competition_solver_cop", "competition_id", "solver_id")->withPivot("nb_benchmarks");
    }

    public function displaysolvers() {

       /* if(Schema::hasTable("display")) {
            $tmp = $this->belongsToMany("App\Models\Solver", "display", "competition_id", "solver_id");
            if($tmp->count() > 0) {
                return $tmp->get();
            }
        }*/
        return $this->solvers();
    }


    public function initResults($solvers) {
        if($this->type == "cop")
            $this->initBestBounds($solvers);
        else $this->initSAT($solvers);
    }

    public function initSAT($solvers) {
        foreach ($this->benchmarks2 as $benchmark) {
            $nbSAT = 0;
            $nbUNSAT = 0;
            foreach ($solvers as $solver_id) {
                $result = DB::select("SELECT * FROM results WHERE solver_id=? and benchmark_id=?", [$solver_id, $benchmark->id]); // Easiest way...

                if ($result == false || $result->bug || $result->unsupported)
                    continue;
                if ($result->status == "SAT")
                    $nbSAT++;
                if ($result->status == "UNSAT")
                    $nbUNSAT++;

            }
            if($nbSAT > 0 && $nbUNSAT > 0)
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
    public function initBestBounds($solvers) {
        foreach ($this->benchmarksCop as $benchmark) {
            $benchmark->optim = 0;
            $minimize = substr(strtoupper($benchmark->type), 0, 3) == "MIN";
            $benchmark->best_bound = null;
            foreach ($solvers as $solver_id) {
                $result = DB::select("SELECT * FROM results_cop WHERE solver_id=? and benchmark_id=?", [$solver_id, $benchmark->id]);
                $result = count($result) == 0 ? false : $result[0];
                if ($result == false || $result->bug || $result->unsupported)
                    continue;
                if ($result->time != -1)
                    $benchmark->optim = 1;
                $bounds = json_decode(str_replace("'", '"', $result->bounds));
                if (count($bounds) == 0)
                    continue;
                $best = $bounds[count($bounds) - 1];
                if ($benchmark->best_bound == null || ($minimize && $benchmark->best_bound > $best->bound) ||
                    (!$minimize && $benchmark->best_bound < $best->bound))
                    $benchmark->best_bound = $best->bound;
            }
            $benchmark->save();
        }
    }
}
