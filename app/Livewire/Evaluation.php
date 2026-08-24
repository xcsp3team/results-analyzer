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
        $this->filters = new Filters();
        $this->initialize_filtering();
    }


    public function initialize_filtering()
    {
        $this->filters->time_limit = $this->evaluation->defaulttime;
        $this->filters->status = "ALL";
        $this->filters->families = $this->evaluation->families();
    }

    #[On('filters_change')]
    public function change_filtering($field, $value)
    {
        if ($field == "status" && $this->filters->status != "ALL") {
            if ($this->filters->status == "UNSAT" && $value == "UNSAT")
                $value = "ALL";
            if ($this->filters->status == "SAT" && $value == "SAT")
                $value = "ALL";
        }
        $this->filters->$field = $value;
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
