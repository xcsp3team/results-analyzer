<?php

namespace App\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class SelectedSolvers extends Component {
    #[Reactive]
    public $selected_solvers;

    public $evaluation;

    public function mount($evaluation, $selected_solvers)
    {
        $this->selected_solvers = $selected_solvers;
        $this->evaluation = $evaluation;
    }

    public function render()
    {
        return view('livewire.selected-solvers');
    }

    public function toggle_selected_solver($id)
    {
        $this->dispatch("toggle_selected_solver", $id);
    }
}
