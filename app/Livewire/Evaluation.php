<?php

namespace App\Livewire;

use App\Misc\Filters;
use App\Models\Evaluation as EvaluationModel;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Evaluation extends Component
{

    public $evaluation;
    public $selected_solvers = [];
    public $filters;
    public $display_sidebar = true;

    public $view = 1;
    public $first = true;


    public function mount(string $slug)
    {
        $this->evaluation = EvaluationModel::where('slug', $slug)->firstOrFail();
        foreach ($this->evaluation->solvers as $solver) {
            $this->selected_solvers[$solver->id] = $solver->id;
        }
        $this->filters = new Filters();
        $this->initialize_filtering();
    }


    #[On('initialize_filters')]
    public function initialize_filtering()
    {
        $this->filters->time_limit = $this->evaluation->defaulttime;
        $this->filters->status = "ALL";
        $this->filters->families = $this->evaluation->families();
        $this->filters->constraints = [];
        $this->filters->are_forbidden = true;
        $this->nb_benchmarks = $this->evaluation->benchmarks()->count();
        $this->filters->expression = "d > 0 and v > 0 and c > 0";
        if ($this->first == false)
            Toaster::success('Filters initialized.');
        $this->first = false;

    }

    #[On('filters_change')]
    public function change_filtering($field, $value, $forbidden = false)
    {
        if ($field == "status" && $this->filters->status != "ALL") {
            if ($this->filters->status == "UNSAT" && $value == "UNSAT")
                $value = "ALL";
            if ($this->filters->status == "SAT" && $value == "SAT")
                $value = "ALL";
        }
        if ($field == "constraints") {
            $this->filters->are_forbidden = $forbidden;
        }
        $this->filters->$field = $value;
        Toaster::success('Filters updated.');
    }

    #[On('toggle_selected_solver')]
    public function toggle_selected_solver($id)
    {
        if (isset($this->selected_solvers[$id]))
            unset($this->selected_solvers[$id]);
        else
            $this->selected_solvers[$id] = $id;
        Toaster::success('Selected solvers updated.');
    }

    #[On("toggle_sidebar")]
    public function toggle_sidebar()
    {
        $this->display_sidebar = !$this->display_sidebar;
    }

    #[On("none-solvers")]
    public function none_solvers()
    {
        $this->selected_solvers = [];
        Toaster::success('Selected solvers updated.');
    }

    #[On("all-solvers")]
    public function all_solvers()
    {
        foreach ($this->evaluation->solvers as $solver)
            $this->selected_solvers[$solver->id] = $solver->id;
        Toaster::success('Selected solvers updated.');

    }

    #[On("view")]
    public function view($view)
    {
        $this->view = $view;
    }

    public function render()
    {
        return view('livewire.evaluation', ["title" => $this->evaluation->name . ". Track " . $this->evaluation->track])->title($this->evaluation->name . ". Track " . $this->evaluation->track);
    }
}
