<?php

namespace App\Livewire\Radar;

use App\Misc\DataPlot;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class RadarCop extends AbstractRadar
{
    public function create_radars()
    {
        $this->families = [];
        foreach ($this->evaluation->families() as $family)
            $this->families[$family] = new DataPlot($family, array_fill(0, count($this->selected_solvers), 0));

        $this->solvers_name = [];
        foreach ($this->selected_solvers as $id) {
            $solver = $this->solvers[$id];
            $this->solvers_name[] = $this->solvers[$solver->id]->name . " " . $this->solvers[$solver->id]->version;
        }


        $all_results = $this->evaluation->all_results_cop($this->filters->time_limit);
        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
            $s_id = 0;

            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                $data = $all_results[$selectedSolver->id][$benchmark->id];
                if($data->bug || $data->unsupported)
                    continue;
                if ($data->status == "UNSAT" && $data->time < $this->filters->time_limit) {
                    $this->families[$benchmark->family]->data[$s_id]++;
                } else {
                    if (str_contains(strtoupper($benchmark->type), "MAX"))
                        $type = "MAXIMIZE";
                    else
                        $type = "MINIMIZE";
                    $best_bound = $this->evaluation->best_bound_cop($benchmark->id, $type, $this->selected_solvers, $this->filters->time_limit);
                    if($best_bound->bound === null)
                        continue;
                    $score = $this->evaluation->score_cop($benchmark->id, $id, $type, $best_bound, $this->filters->time_limit);
                    $this->families[$benchmark->family]->data[$s_id] += ($score->optimum + $score->bb1 + $score->bb2);
                }
                $s_id++;
            }
        }
        foreach ($this->families as $name => $family) {
            $this->dispatch("radar-$name-updated",
                series: [['name' => $family->name, 'data' => $family->data]],
                xaxis: $this->solvers_name
            );
        }
    }
}
