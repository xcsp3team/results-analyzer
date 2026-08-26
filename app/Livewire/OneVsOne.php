<?php

namespace App\Livewire;

use App\Misc\DataCactus;
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

    public $series;

    public $solver_x = 7;
    public $solver_y = 40;

    public $name_x = null;
    public $name_y = null;

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

        $this->series = [];
        $this->series[] = new DataCactus("SAT");
        $this->series[] = new DataCactus("UNSAT");
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
                $this->series[0]->data[] = [$time_x, $time_y, $benchmark->name];
            else
                $this->series[1]->data[] = [$time_x, $time_y, $benchmark->name];
        }
        $this->dispatch('scatter-updated',
            series: array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $this->series),
        );

    }

    public function render()
    {
        if ($this->solver_x != null)
            $this->create_scatter();
        return view('livewire.one-vs-one');
    }
}
