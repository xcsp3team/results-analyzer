<?php

namespace App\Misc;

use Livewire\Wireable;

class Data implements Wireable
{
    public $value;
    public $class;
    public $attributes;
    public $value_sort;

    public function __construct($v = 0, $c = "", $a = "", $vs = null)
    {
        $this->value = $v;
        $this->class = $c;
        $this->attributes = $a;
        $this->value_sort = $vs;
    }

    public function toLivewire()
    {
        return [
            'value' => $this->value,
            'class' => $this->class,
            'attributes' => $this->attributes,
            'value_sort' => $this->value_sort,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['value'], $value['class'], $value['attributes'], $value['value_sort']);
    }
}



