<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benchmark extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'fullname', 'family', 'status', 'nb_variables', 'nb_constraints', 'useles_vars',
        'info_variables', 'info_constraints'];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function max_degree()
    {
        preg_match_all('/#(\d+):/', $this->info_domains, $matches);
        return (int)end($matches[1]);
    }
}
