<?php

namespace App\Livewire\Details;

use App\Misc\Data;
use App\Misc\DataHeader;


class DetailsCop extends AbstractDetails
{

    public function create_detailed_results()
    {
        $all_results = $this->evaluation->all_results_cop($this->filters->time_limit);
        $this->detailed_results = [];
        $this->header_results = [
            new DataHeader("Instance", "left"),
            new DataHeader("V"),
            new DataHeader("C"),
            new DataHeader("Optimisation", "left"),
            new DataHeader("Best Bound"),
        ];
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $this->header_results[] = new DataHeader($selectedSolver->name . " " . $selectedSolver->version);
        }

        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark) || ($this->instance_name != null) && str_contains($benchmark->name, $this->instance_name) == false)
                continue;
            $tmp = [new Data($benchmark->name), new Data($benchmark->nb_variables), new Data($benchmark->nb_clauses), new Data($benchmark->type), new Data($benchmark->bounds)];

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

                // UNSAT CASE
                if ($data->status == "UNSAT" && $data->time <= $this->filters->time_limit) {
                    $cell = new Data("UNSAT (1) " . $data->time, "text-green-500");
                    continue;
                }

                // No bound found
                if ($data->bound == null) {
                    $tmp[] = new Data("-", "opacity-30");
                    continue;
                }

                $cell = $data->bound;
                $score =

                if ($data->bug) {
                    $tmp[] = new Data($cell, "bg-red-500 opacity-50");
                    continue;
                }
                if ($data->time <= $best && $data->time <= $this->filters->time_limit) {
                    $tmp[] = new Data($cell, "text-green-500");
                    continue;
                }
                if ($data->time <= $this->filters->time_limit)
                    $tmp[] = new Data($cell);
                else
                    $tmp[] = new Data("-", "opacity-30");
            }
            $this->detailed_results[] = $tmp;
        }

    }

}
