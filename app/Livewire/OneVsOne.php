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

    public $selected_families;
    public $per_families;

    public $solver_x = null;
    public $solver_y = null;

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
        $this->selected_families = [];
        $i = 0;
        foreach ($this->evaluation->families() as $family) {
            if (in_array($family, $this->filters->families))
                $this->selected_families[$family] = $i++;
        }

        $this->per_families["SATX"] = new DataPlot($this->name_x . " SAT", array_fill(0, count($this->selected_families), 0), $this->name_x);
        $this->per_families["SATY"] = new DataPlot($this->name_y . " SAT", array_fill(0, count($this->selected_families), 0), $this->name_y);
        $this->per_families["UNSATX"] = new DataPlot($this->name_x . " UNSAT", array_fill(0, count($this->selected_families), 0), $this->name_x);
        $this->per_families["UNSATY"] = new DataPlot($this->name_y . " UNSAT", array_fill(0, count($this->selected_families), 0), $this->name_y);

        $all_results = $this->evaluation->all_results();
        foreach ($this->evaluation->benchmarks2 as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
            $family = $benchmark->family;
            $pos = $this->selected_families[$family];
            $data_x = $all_results[$this->solver_x][$benchmark->id];
            $data_y = $all_results[$this->solver_y][$benchmark->id];
            if ($data_x->time <= $this->filters->time_limit && $data_x->bug == false) {
                if ($benchmark->status == "SAT")
                    $this->per_families["SATX"]->data[$pos]++;
                else
                    $this->per_families["UNSATX"]->data[$pos]++;
            }
            if ($data_y->time <= $this->filters->time_limit && $data_y->bug == false) {
                if ($benchmark->status == "SAT")
                    $this->per_families["SATY"]->data[$pos]++;
                else
                    $this->per_families["UNSATY"]->data[$pos]++;
            }
        }

        $categories = array_flip($this->selected_families); // pos => famille
        ksort($categories);
        $categories = array_values($categories); // liste ordonnée par position

        $this->dispatch('per_families-updated',
            series: array_values(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data, "group" => $s->group], $this->per_families)),
            selected_families: $categories,
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
