<?php

namespace App\Livewire\Radar;

use App\Misc\DataPlot;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class RadarSat extends AbstractRadar
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


        $all_results = $this->evaluation->all_results();
        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
            $s_id = 0;
            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                $data = $all_results[$selectedSolver->id][$benchmark->id];
                if ($data->bug == 0 && $data->time <= $this->filters->time_limit && $data->unsupported == 0)
                    $this->families[$benchmark->family]->data[$s_id]++;
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
