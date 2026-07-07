<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benchmark_cop extends Model
{
    use HasFactory;
    protected $table = "benchmarks_cop";
    protected $fillable = ['family', "name", "nb_constraints", "nb_variables", "info_domains", "info_constraints",
        "type", "useless_vars", "fullname", "type", "best_bound", "optim"];

    public function competition() {
        return $this->belongsTo(Competition::class);
    }
}
