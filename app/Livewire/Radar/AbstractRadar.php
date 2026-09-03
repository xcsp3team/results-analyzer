<?php

namespace App\Livewire\Radar;

use App\Misc\DataPlot;
use Livewire\Attributes\Reactive;
use Livewire\Component;

abstract class AbstractRadar  extends Component
{

    public $evaluation;

    #[Reactive]
    public $filters;

    #[Reactive]
    public $selected_solvers;

    public $families;

    public $solvers;

    public $solvers_name;

    public function mount($filters, $evaluation, $selected_solvers)
    {
        $this->filters = $filters;
        $this->selected_solvers = $selected_solvers;
        $this->evaluation = $evaluation;
        foreach ($this->evaluation->solvers as $solver)
            $this->solvers[$solver->id] = $solver;
    }

    public abstract function create_radars();

    public function render()
    {
        $this->create_radars();
        return view('livewire.radar');
    }
}
