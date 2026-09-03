<?php

namespace App\Livewire\Details;

use Livewire\Attributes\Reactive;
use Livewire\Component;

abstract class AbstractDetails extends Component
{
    public $detailed_results = [];
    public $header_results = [];
    public $solvers;
    public $evaluation;
    public $instance_name = null;

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

    public abstract function create_detailed_results();


    public function render()
    {
        $this->create_detailed_results();
        return view('livewire.detailed');
    }

}
