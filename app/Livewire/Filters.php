<?php

namespace App\Livewire;

use Livewire\Wireable;

class Filters implements Wireable {
    public $time_limit;

    public function __construct($time_limit)
    {
        $this->time_limit = $time_limit;
    }

    public function toLivewire()
    {
        return [
            'time_limit' => $this->time_limit,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['time_limit']);
    }

    public function is_filtered($benchmark)
    {
        return false;
    }
}
