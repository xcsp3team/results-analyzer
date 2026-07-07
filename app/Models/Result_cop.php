<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result_cop extends Model
{
    protected $table = "results_cop";


    public function benchmark() {
        return $this->belongsTo(Benchmark_cop::class);
    }

    public function solver() {
        return $this->belongsTo(Solver::class);
    }
}
