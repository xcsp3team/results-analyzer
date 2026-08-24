<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Reactive;
use Livewire\Component;


class Filtering extends Component {
    public $evaluation;

    #[Reactive]
    public $filters;

    public $all_families;

    public function mount($filters, $evaluation)
    {
        $this->filters = $filters;
        $this->evaluation = $evaluation;
        $this->all_families = $this->evaluation->families();
    }

    public function render()
    {
        return view('livewire.filtering');
    }

    public function change($field, $value)
    {
        $this->dispatch("filters_change", $field, $value);
    }
}
