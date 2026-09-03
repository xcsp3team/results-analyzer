<?php

namespace App\Misc;

use Livewire\Wireable;

class DataPlot implements Wireable
{

    public $name;
    public $data;
    public $group;
    public $color;

    public function __construct($n, $d = [], $g = null, $c = null)
    {
        $this->name = $n;
        $this->data = $d;
        $this->group = $g;
        $this->color = $c;
    }

    public function toLivewire()
    {
        return [
            'name' => $this->name,
            'data' => $this->data,
            'group' => $this->group,
            'color' => $this->color,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['name'], $value['data'], $value['group'], $value['color']);
    }
}
