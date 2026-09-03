<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Unguarded;

#[Unguarded]
class Result extends Model
{
    
    public function benchmark()
    {
        return $this->belongsTo(Benchmark::class);
    }

    public function solver()
    {
        return $this->belongsTo(Solver::class);
    }
}
