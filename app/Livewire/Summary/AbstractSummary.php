<?php

namespace App\Livewire\Summary;

use Livewire\Attributes\Reactive;
use Livewire\Component;

abstract class AbstractSummary extends Component {
    public $summary;
    public $header_summary;
    public $solvers;

    public $evaluation;

    #[Reactive]
    public $filters;

    #[Reactive]
    public $selected_solvers;

    public function mount($filters, $evaluation, $selected_solvers)
    {
        $this->filters = $filters;
        $this->selected_solvers = $selected_solvers;
        $this->evaluation = $evaluation;
        foreach ($this->evaluation->solvers as $solver)
            $this->solvers[$solver->id] = $solver;
    }

    public abstract function create_summary();

    public function render()
    {
        $this->create_summary();
        return view('livewire.summary');
    }

}
