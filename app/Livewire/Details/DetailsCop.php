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

        $selected_solvers_string = implode(",", $this->selected_solvers);

        $icon_min = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 17.3497C9 17.3497 15.9383 17.8924 16.9154 16.9154C17.8924 15.9383 17.3496 9 17.3496 9M16.5 16.5L6.5 6.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></svg>';
        $icon_max = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 6.65032C9 6.65032 15.9383 6.10759 16.9154 7.08463C17.8924 8.06167 17.3496 15 17.3496 15M16.5 7.5L6.5 17.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></svg>';

        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark) || ($this->instance_name != null) && str_contains($benchmark->name, $this->instance_name) == false)
                continue;
            $icon = str_contains(strtoupper($benchmark->type), "MIN") ? $icon_min : $icon_max;
            $tmp = [new Data($benchmark->name), new Data($benchmark->nb_variables), new Data($benchmark->nb_clauses)];
            $tmp[] = new Data($benchmark->type . "&nbsp;&nbsp;$icon", "flex items-center", 'wire:click=$dispatch(\'openModal\',{component:\'evolution\',arguments:{selected_solvers:[' . $selected_solvers_string . '],benchmark_id:' . $benchmark->id . ',time_limit:' . $this->filters->time_limit . '}})');

            if ($benchmark->status == "UNSAT")
                $tmp[] = new Data("UNSAT", "text-green-500");
            else
                $tmp[] = new Data($benchmark->best_bound, $benchmark->status == "OPTIMUM" ? "text-green-500" : "");

            $type = $benchmark->get_type();
            $best_bound = $this->evaluation->best_bound_cop($benchmark->id, $type, $this->selected_solvers, $this->filters->time_limit);

            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                $data = $all_results[$selectedSolver->id][$benchmark->id];
                if ($data->unsupported) {
                    $tmp[] = new Data("U", "", "", 0);
                    continue;
                }

                // UNSAT CASE
                if ($data->status == "UNSAT" && $data->time <= $this->filters->time_limit) {
                    $tmp[] = new Data("UNSAT (1) " . $data->time . "s", "text-green-500", 1);
                    continue;
                }

                // No bound found
                if ($data->bound === null) {
                    $tmp[] = new Data("(0)", "dark:opacity-60 opacity-30", "", 0.);
                    continue;
                }

                $score = $this->evaluation->score_cop($benchmark->id, $id, $type, $best_bound, $this->filters->time_limit);
                $nb = $score->optimum + $score->bb1 + $score->bb2 / 2;
                if ($data->time != -1 && $data->time <= $this->filters->time_limit)
                    $time = $data->bound_time . "s - " . $data->time . "s";
                else $time = $data->bound_time . "s";
                $cell = $data->bound . " ($nb) " . $time;
                if ($data->bug) {
                    $tmp[] = new Data($cell, "bg-red-700 opacity-40", "", 0);
                    continue;
                }
                if ($data->time != -1 && $data->time <= $this->filters->time_limit) {
                    $tmp[] = new Data($cell, "text-green-500", "", 2);
                    continue;
                }
                if ($data->bound == $best_bound->bound)
                    $tmp[] = new Data($cell, "", "", $nb);
                else
                    $tmp[] = new Data($cell, "dark:opacity-60 opacity-30", "", -1);
            }
            $this->detailed_results[] = $tmp;
        }
    }

}
