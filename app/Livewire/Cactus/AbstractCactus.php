<?php

namespace App\Livewire\Cactus;

use App\Misc\DataPlot;
use Livewire\Attributes\Reactive;
use Livewire\Component;

abstract class AbstractCactus extends Component
{
    public $evaluation;

    #[Reactive]
    public $filters;

    public $solvers;

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



}
