<?php

namespace App\Livewire;

use App\Models\Competition;
use Livewire\Attributes\On;
use Livewire\Component;

class Evaluation extends Component {

    public $evaluation;
    public $selected_solvers = [];
    public $filters;


    public function mount(string $slug)
    {
        $this->evaluation = Competition::where('slug', $slug)->firstOrFail();
        foreach ($this->evaluation->solvers2 as $solver) {
            $this->selected_solvers[$solver->id] = $solver->id;
        }
        $this->filters = new Filters($this->evaluation->defaulttime);
    }


    #[On('filters_change')]
    public function change_filtering($changes)
    {
        $f = $changes["field"];
        $v = $changes["value"];
        $this->filters->$f = $v;
    }

    #[On('toggle_selected_solver')]
    public function toggle_selected_solver($id)
    {
        if (isset($this->selected_solvers[$id]))
            unset($this->selected_solvers[$id]);
        else
            $this->selected_solvers[$id] = $id;
    }

    #[On("none-solvers")]
    public function none_solvers()
    {
        $this->selected_solvers = [];
    }

    #[On("all-solvers")]
    public function all_solvers()
    {
        foreach ($this->evaluation->solvers2 as $solver)
            $this->selected_solvers[$solver->id] = $solver->id;
    }


    public function render()
    {
        return view('livewire.evaluation');
    }
}
