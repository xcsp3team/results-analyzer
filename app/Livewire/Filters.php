<?php

namespace App\Livewire;

use Livewire\Wireable;

class Filters implements Wireable {
    public $time_limit;
    public $status;
    public $families;


    public function __construct($time_limit = 0, $status = "ALL", $families = [])
    {
        $this->time_limit = $time_limit;
        $this->status = $status;
        $this->families = $families;
    }

    public function toLivewire()
    {
        return [
            'time_limit' => $this->time_limit,
            'status' => $this->status,
            'families' => $this->families,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['time_limit'], $value['status'], $value['families']);
    }

    public function is_filtered($benchmark)
    {
        if ($this->status == "UNSAT" && $benchmark->status != "UNSAT")
            return true;
        if ($this->status == "SAT" && $benchmark->status != "SAT")
            return true;
        if (in_array($benchmark->family, $this->families) == false)
            return true;
        return false;
    }
}
