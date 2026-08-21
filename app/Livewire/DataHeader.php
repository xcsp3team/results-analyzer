<?php

namespace App\Livewire;

use Livewire\Wireable;

class DataHeader implements Wireable {
    public $value;
    public $sortable;
    public $align;

    public function __construct($v = 0, $a = "text-right", $s = true)
    {
        $this->value = $v;
        $this->sortable = $s;
        $this->align = $a;
    }

    public function toLivewire()
    {
        return [
            'value' => $this->value,
            'align' => $this->align,
            'sortable' => $this->sortable,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['value'], $value['align'], $value['sortable']);
    }
}
