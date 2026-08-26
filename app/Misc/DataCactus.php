<?php

namespace App\Misc;

use Livewire\Wireable;

class DataCactus implements Wireable
{

    public $name;
    public $data;

    public function __construct($n, $d = [])
    {
        $this->name = $n;
        $this->data = $d;
    }

    public function toLivewire()
    {
        return [
            'name' => $this->name,
            'data' => $this->data
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['name'], $value['data']);
    }
}
