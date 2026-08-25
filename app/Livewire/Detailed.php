<?php

namespace App\Livewire;

use App\Misc\Data;
use App\Misc\DataHeader;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Detailed extends Component
{
    public $detailed_results = [];
    public $header_results = [];
    public $solvers;
    public $evaluation;
    public $instance_name = null;

    #[Reactive]
    public $filters;

    #[Reactive]
    public $selected_solvers;

    public function mount($filters, $evaluation, $selected_solvers)
    {
        $this->filters = $filters;
        $this->selected_solvers = $selected_solvers;
        $this->evaluation = $evaluation;
        foreach ($this->evaluation->solvers2 as $solver)
            $this->solvers[$solver->id] = $solver;

    }

    public function createDetailedResults()
    {
        $all_results = $this->evaluation->all_results();
        $this->detailed_results = [];
        $this->header_results = [
            new DataHeader("Instance", "left"),
            new DataHeader("V"),
            new DataHeader("C"),
            new DataHeader("Status")
        ];
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $this->header_results[] = new DataHeader($selectedSolver->name . " " . $selectedSolver->version);
        }

        foreach ($this->evaluation->benchmarks2 as $benchmark) {
            if ($this->filters->is_filtered($benchmark) || ($this->instance_name != null) && str_contains($benchmark->name, $this->instance_name) == false)
                continue;
            $tmp = [new Data($benchmark->name), new Data($benchmark->nb_variables), new Data($benchmark->nb_clauses), new Data($benchmark->status)];

            $best = $this->filters->time_limit;
            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                if ($all_results[$selectedSolver->id][$benchmark->id]->time < $best)
                    $best = $all_results[$selectedSolver->id][$benchmark->id]->time;
            }

            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                $data = $all_results[$selectedSolver->id][$benchmark->id];
                if ($data->unsupported) {
                    $tmp[] = new Data("U");
                    continue;
                }
                if ($data->bug) {
                    $tmp[] = new Data($data->time, "bg-red-500 opacity-50");
                    continue;
                }
                if ($data->time <= $best && $data->time <= $this->filters->time_limit) {
                    $tmp[] = new Data($data->time, "text-green-500");
                    continue;
                }
                if ($data->time <= $this->filters->time_limit)
                    $tmp[] = new Data($data->time);
                else
                    $tmp[] = new Data("-", "opacity-30");
            }
            $this->detailed_results[] = $tmp;
        }

    }

    public function render()
    {
        $this->createDetailedResults();
        return view('livewire.detailed');
    }
}
