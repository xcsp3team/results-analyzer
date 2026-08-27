<?php

namespace App\Livewire;

use App\Misc\DataPlot;
use App\Models\Solver;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class OneVsOne extends Component
{
    public $evaluation;

    #[Reactive]
    public $filters;

    #[Reactive]
    public $selected_solvers;

    public $nb_benchmarks;

    public $scatter;
    public $per_constraints;

    public $per_families;

    public $solver_x = 7;
    public $solver_y = 40;

    public $name_x = "SX";
    public $name_y = "SY";

    public $xaxis;

    public function mount($filters, $evaluation, $selected_solvers)
    {
        $this->filters = $filters;
        $this->selected_solvers = $selected_solvers;
        $this->evaluation = $evaluation;
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

    public function create_scatter()
    {
        $all_results = $this->evaluation->all_results();

        $this->scatter = [];
        $this->scatter[] = new DataPlot("SAT");
        $this->scatter[] = new DataPlot("UNSAT");
        foreach ($this->evaluation->benchmarks2 as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
            $data_x = $all_results[$this->solver_x][$benchmark->id];
            $data_y = $all_results[$this->solver_y][$benchmark->id];
            if (($data_x->time > $this->filters->time_limit && $data_y->time > $this->filters->time_limit) ||
                ($data_x->bug && $data_y->bug))
                continue;
            $time_x = min($data_x->time, $this->filters->time_limit);
            $time_y = min($data_y->time, $this->filters->time_limit);
            if ($benchmark->status == "SAT")
                $this->scatter[0]->data[] = [$time_x, $time_y, $benchmark->name];
            else
                $this->scatter[1]->data[] = [$time_x, $time_y, $benchmark->name];
        }
        $this->dispatch('scatter-updated',
            series: array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $this->scatter),
        );
    }

    public function create_per_constraints()
    {
        $all_results = $this->evaluation->all_results();
        $this->per_constraints = [];
        foreach ($this->evaluation->benchmarks2 as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;

            $data_x = $all_results[$this->solver_x][$benchmark->id];
            $data_y = $all_results[$this->solver_y][$benchmark->id];
        }

        $this->dispatch('per_constraints-updated',
            series: array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $this->per_constraints),
        );

    }

    public function create_per_families()
    {
        $all_results = $this->evaluation->all_results();
        $this->per_families = [];
        foreach ($this->evaluation->benchmarks2 as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
            $family = $benchmark->family;
            if (isset($this->per_families[$family . "_SATX"]) == false) {
                $this->per_families[$family . "_SATX"] = new DataPlot($this->name_x, [], $family);
                $this->per_families[$family . "_SATY"] = new DataPlot($this->name_y, [], $family);
                $this->per_families[$family . "_UNSATX"] = new DataPlot($this->name_x, [], $family);
                $this->per_families[$family . "_UNSATY"] = new DataPlot($this->name_y, [], $family);
            }
            $data_x = $all_results[$this->solver_x][$benchmark->id];
            $data_y = $all_results[$this->solver_y][$benchmark->id];
            if ($data_x->time <= $this->filters->time_limit && $data_x->bug == false) {
                if ($benchmark->status == "SAT")
                    $this->per_families[$family . "_SATX"]->data[] = $data_x->time;
                else
                    $this->per_families[$family . "_UNSATX"]->data[] = $data_x->time;
            }
            if ($data_y->time <= $this->filters->time_limit && $data_y->bug == false) {
                if ($benchmark->status == "SAT")
                    $this->per_families[$family . "_SATY"]->data[] = $data_y->time;
                else
                    $this->per_families[$family . "_UNSATY"]->data[] = $data_y->time;
            }
        }
        $this->dispatch('per_families-updated',
            series: array_values(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data, "group" => $s->group], $this->per_families)),
        );
    }

    public function render()
    {
        if ($this->solver_x != null) {
            $this->create_scatter();
            $this->create_per_families();
        }
        return view('livewire.one-vs-one');
    }
}
