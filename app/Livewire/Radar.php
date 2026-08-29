<?php

namespace App\Livewire;

use App\Misc\DataPlot;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Radar extends Component
{
    public $evaluation;

    #[Reactive]
    public $filters;

    #[Reactive]
    public $selected_solvers;

    public $families;

    public $solvers;

    public $solvers_name;

    public function mount($filters, $evaluation, $selected_solvers)
    {
        $this->filters = $filters;
        $this->selected_solvers = $selected_solvers;
        $this->evaluation = $evaluation;
        foreach ($this->evaluation->solvers as $solver)
            $this->solvers[$solver->id] = $solver;
    }

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

    public function render()
    {
        $this->create_radars();
        return view('livewire.radar');
    }
}
