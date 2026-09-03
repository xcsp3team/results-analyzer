<?php

namespace App\Livewire\Cactus;

use App\Misc\DataPlot;

class CactusSat extends AbstractCactus
{
    public $xaxis;

    public $series;
    public function create_cactus()
    {
        $max = 0;
        $all_results = $this->evaluation->all_results();

        $this->series = [];
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $tmp = new DataPlot($selectedSolver->name . " " . $selectedSolver->version);
            $values = [];
            foreach ($this->evaluation->benchmarks as $benchmark) {
                if ($this->filters->is_filtered($benchmark))
                    continue;
                $data = $all_results[$selectedSolver->id][$benchmark->id];
                if ($data->bug == 0 && $data->time < $this->filters->time_limit && $data->unsupported == 0)
                    $values[] = $data->time;
            }
            if (count($values) > $max)
                $max = count($values);
            sort($values);
            $tmp->data = $values;
            $this->series[] = $tmp;
        }
        $this->xaxis = [];
        for ($i = 1; $i <= $max; $i++)
            $this->xaxis[] = $i;
        $this->dispatch('cactus-updated',
            series: array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $this->series),
            xaxis: $this->xaxis
        );
    }

    public function render()
    {
        $this->create_cactus();
        return view('livewire.cactus');
    }
}
