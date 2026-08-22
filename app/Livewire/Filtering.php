<?php

namespace App\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;


class Filtering extends Component {
    public $evaluation;

    #[Reactive]
    public $filters;

    #[Reactive]
    public $selected_solvers;

    public function mount($filters, $evaluation, $selected_solvers)
    {
        $this->filters = $filters;
        $this->evaluation = $evaluation;
        $this->selected_solvers = $selected_solvers;
    }

    public function render()
    {
        return view('livewire.filtering');
    }

    public function change($field, $value)
    {
        $this->dispatch("filters_change", $field, $value);
    }


    public function toggle_selected_solver($id)
    {
        $this->dispatch("toggle_selected_solver", $id);
    }
}
