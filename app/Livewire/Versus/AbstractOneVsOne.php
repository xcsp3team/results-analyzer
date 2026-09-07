<?php

namespace App\Livewire\Versus;

use App\Misc\DataPlot;
use App\Models\Solver;
use Livewire\Attributes\Reactive;
use Livewire\Component;

abstract class AbstractOneVsOne extends Component
{
    public $evaluation;

    #[Reactive]
    public $filters;

    #[Reactive]
    public $selected_solvers;


    public $scatter;
    public $per_constraints;
    public $per_families;

    public $selected_families;
    public $selected_constraints;


    public $solver_x = 7;
    public $solver_y = 19;

    public $name_x;
    public $name_y;

    public $xaxis;

    public function mount($filters, $evaluation, $selected_solvers)
    {
        $this->filters = $filters;
        $this->selected_solvers = $selected_solvers;
        $this->evaluation = $evaluation;
    }

    public function close()
    {
        $this->solver_x = null;
        $this->solver_y = null;
    }

    public function set_solvers()
    {
        if ($this->solver_x == $this->solver_y) {
            $this->solver_x = null;
            $this->solver_y = null;
        } else {
            $tmp = Solver::find($this->solver_x);
            $this->name_x = $tmp->name . " " . $tmp->version;
            $tmp = Solver::find($this->solver_y);
            $this->name_y = $tmp->name . " " . $tmp->version;

        }
    }

    abstract public function create_scatter();

    abstract public function create_per_constraints();

    abstract public function create_per_families();

    public function render()
    {
        if ($this->solver_x != null) {
            $this->create_scatter();
            $this->create_per_families();
            $this->create_per_constraints();
        }
        return view('livewire.one-vs-one');
    }
}
