<?php

namespace App\Livewire;

use Livewire\Wireable;

class Data implements Wireable {
    public $value;
    public $class;

    public function __construct($v = 0, $c = "")
    {
        $this->value = $v;
        $this->class = $c;
    }

    public function toLivewire()
    {
        return [
            'value' => $this->value,
            'class' => $this->class,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['value'], $value['class']);
    }
}



