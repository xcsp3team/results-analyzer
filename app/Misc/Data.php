<?php

namespace App\Misc;

use Livewire\Wireable;

class Data implements Wireable
{
    public $value;
    public $class;
    public $attributes;

    public function __construct($v = 0, $c = "", $a = "")
    {
        $this->value = $v;
        $this->class = $c;
        $this->attributes = $a;
    }

    public function toLivewire()
    {
        return [
            'value' => $this->value,
            'class' => $this->class,
            'attributes' => $this->attributes,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['value'], $value['class'], $value['attributes']);
    }
}



