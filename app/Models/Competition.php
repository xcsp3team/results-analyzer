<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = ["name", "track", "type", "slug", "defaulttime", "public"];


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

    public function displaysolvers() {
        if(Schema::hasTable("display")) {
            $tmp = $this->belongsToMany("App\Models\Solver", "display", "competition_id", "solver_id");
            if($tmp->count() > 0) {
                return $tmp->get();
            }
        }
        return $this->solvers();


    }

}
