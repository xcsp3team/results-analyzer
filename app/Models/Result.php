<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public function benchmark() {
        return $this->belongsTo(Benchmark::class);
    }

    public function solver() {
        return $this->belongsTo(Solver::class);
    }
}
