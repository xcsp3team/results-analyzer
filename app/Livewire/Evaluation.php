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

    public $view = 2;
    public $first = true;

    public $radar_component;
    public $versus_component;
    public $nb_benchmarks;


    public function mount(string $slug)
    {
        $this->evaluation = EvaluationModel::where('slug', $slug)->firstOrFail();
        foreach ($this->evaluation->solvers as $solver) {
            $this->selected_solvers[$solver->id] = $solver->id;
        }
        $this->filters = new Filters($this->evaluation->get_type());
        $this->initialize_filtering();
        $this->radar_component = "radar.radar-" . $this->evaluation->get_type();
        $this->versus_component = "versus.one-vs-one-" . $this->evaluation->get_type();
    }


    #[On('initialize_filters')]
    public function initialize_filtering()
    {
        $this->filters->time_limit = $this->evaluation->defaulttime;
        $this->filters->families = $this->evaluation->families();
        $this->filters->constraints = [];
        $this->filters->are_forbidden = true;
        $this->filters->expression = "d > 0 and v > 0 and c > 0";

        $this->filters->status = "ALL";
        $this->filters->type = "ALL";
        $this->filters->enabled = false;
        if ($this->first == false)
            Toaster::success('Filters initialized.');
        $this->first = false;
        $this->nb_benchmarks = $this->evaluation->benchmarks()->count();

    }

    #[On('filters_change')]
    public function change_filtering($field, $value, $forbidden = false)
    {
        if ($field == "status" && $this->filters->status != "ALL") {
            if ($this->filters->status == $value)
                $value = "ALL";
        }
        if ($field == "type" && $this->filters->type != "ALL") {
            if ($this->filters->type == $value)
                $value = "ALL";
        }

        if ($field == "time_limit" && $this->evaluation->type == "cop") {
            $this->evaluation->all_results_cop($value);
        }

        if ($field == "constraints") {
            $this->filters->are_forbidden = $forbidden;
        }
        $this->filters->$field = $value;
        $this->filters->enabled = true;
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
        return view('livewire.evaluation', ["title" => $this->evaluation->name . " - " . $this->evaluation->track])->title($this->evaluation->name . ". Track " . $this->evaluation->track);
    }
}
