<?php

namespace App\Livewire;

use Livewire\Wireable;

class Filters implements Wireable {
    public $time_limit;
    public $status;
    public $families;
    public $constraints;
    public $are_forbidden;


    public function __construct($time_limit = 0, $status = "ALL", $families = [], $constraints = [], $are_forbidden = false)
    {
        $this->time_limit = $time_limit;
        $this->status = $status;
        $this->families = $families;
        $this->constraints = $constraints;
        $this->are_forbidden = $are_forbidden;
    }

    public function toLivewire()
    {
        return [
            'time_limit' => $this->time_limit,
            'status' => $this->status,
            'families' => $this->families,
            'constraints' => $this->constraints,
            'are_forbidden' => $this->are_forbidden,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['time_limit'], $value['status'], $value['families'], $value['constraints'], $value['are_forbidden']);
    }

    public function is_filtered($benchmark)
    {
        if ($this->status == "UNSAT" && $benchmark->status != "UNSAT")
            return true;
        if ($this->status == "SAT" && $benchmark->status != "SAT")
            return true;
        if (in_array($benchmark->family, $this->families) == false)
            return true;

        if ($this->are_forbidden) {
            foreach ($this->constraints as $constraint) {
                if (str_contains($benchmark->info_constraints, $constraint) != false)
                    return true;
            }
        } else {
            foreach ($this->constraints as $constraint) {
                if (str_contains($benchmark->info_constraints, $constraint) == false)
                    return true;
            }
        }

        return false;
    }
}
