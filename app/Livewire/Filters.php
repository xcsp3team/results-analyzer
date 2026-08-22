<?php

namespace App\Livewire;

use Livewire\Wireable;

class Filters implements Wireable {
    public $time_limit;
    public $status;


    public function __construct($time_limit, $status = "ALL")
    {
        $this->time_limit = $time_limit;
        $this->status = $status;
    }

    public function toLivewire()
    {
        return [
            'time_limit' => $this->time_limit,
            'status' => $this->status,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['time_limit'], $value['status']);
    }

    public function is_filtered($benchmark)
    {
        if ($this->status == "UNSAT" && $benchmark->status != "UNSAT")
            return true;
        if ($this->status == "SAT" && $benchmark->status != "SAT")
            return true;

        return false;
    }
}
