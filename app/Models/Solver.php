<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Solver extends Model {
    use HasFactory;

    protected $fillable = ["name", "version", "params", "authors"];

    public function results($c)
    {

        if (gettype($c) == "string")
            $c = Evaluation::findOrFail($c);

        return DB::select("select results.solver_id, results.benchmark_id, results.status, results.time, results.bounds, results.unsupported,results.bug "
            . "from benchmarks left join results on benchmark_id=benchmarks.id where evaluation_id=? and solver_id=? order by benchmark_id"
            , [$c->id, $this->id]);
    }


    public function competitions()
    {
    }

}
