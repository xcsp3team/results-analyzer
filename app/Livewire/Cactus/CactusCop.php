<?php

namespace App\Livewire\Cactus;

use App\Misc\DataPlot;

class CactusCop extends AbstractCactus
{

    public $series_optimum;
    public $series_search;
    public function create_cactus() {
        $all_results = $this->evaluation->all_results_cop($this->filters->time_limit);

        $this->series_optimum = [];
        $this->series_search = [];
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $tmp_optimum = new DataPlot($selectedSolver->name . " " . $selectedSolver->version);
            $tmp_search = new DataPlot($selectedSolver->name . " " . $selectedSolver->version);
            $values_optimum = [];
            $values_search = [];
            foreach ($this->evaluation->benchmarks as $benchmark) {
                if ($this->filters->is_filtered($benchmark))
                    continue;
                $data = $all_results[$id][$benchmark->id];
                if($data->bug)
                    continue;
                if($data->time != -1 && $data->time < $this->filters->time_limit)
                    $values_optimum[] = $data->time;
                if (str_contains(strtoupper($benchmark->type), "MAX"))
                    $type = "MAXIMIZE";
                else
                    $type = "MINIMIZE";
                $best_bound = $this->evaluation->best_bound_cop($benchmark->id, $type, $this->selected_solvers, $this->filters->time_limit);
                if ($best_bound->bound === null)
                    continue;
                $score = $this->evaluation->score_cop($benchmark->id, $id, $type, $best_bound, $this->filters->time_limit);
                if($score->optimum || ($data->status == "UNSAT" && $data->time < $this->filters->time_limit))
                    $values_search[]=$data->time;
                else
                    if($score->bb1 + $score->bb2 > 0)
                        $values_search[]=$data->bound_time;


            }
            sort($values_optimum);
            sort($values_search);

            $tmp_optimum->data = $values_optimum;
            $tmp_search->data = $values_search;
            $this->series_optimum[] = $tmp_optimum;
            $this->series_search[] = $tmp_search;
        }
        $this->dispatch('cactus-opt-updated',
            series_optimum: array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $this->series_optimum),
            //xaxis: $this->xaxis
        );
        $this->dispatch('cactus-search-updated',
            series_search: array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $this->series_search),
        //xaxis: $this->xaxis
        );
    }
    public function render() {
        $this->create_cactus();
        return view('livewire.cactus-cop');
    }

}
