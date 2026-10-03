<?php

namespace App\Livewire;

use App\Misc\DataPlot;
use App\Models\Benchmark;
use App\Models\Solver;
use Illuminate\Support\Facades\DB;
use LivewireUI\Modal\ModalComponent;

class Evolution extends ModalComponent
{
    public $selected_solvers;
    public $time_limit;
    public $benchmark_id;
    public $benchmark;

    public $series;


    public function mount($selected_solvers, $time_limit, $benchmark_id)
    {
        $this->selected_solvers = $selected_solvers;
        $this->time_limit = $time_limit;
        $this->benchmark_id = $benchmark_id;
        $this->benchmark = Benchmark::find($benchmark_id);
    }

    public function create_data()
    {
        $this->series = [];
        $solvers = Solver::whereIn('id', $this->selected_solvers)->get()->keyBy('id');

        $bounds = DB::table('result_bounds as rb')
            ->join('results as r', 'r.id', '=', 'rb.result_id')
            ->where('r.benchmark_id', $this->benchmark_id)
            ->whereIn('r.solver_id', $this->selected_solvers)
            ->where('rb.time', '<=', $this->time_limit)
            ->orderBy('rb.time')
            ->get(['r.solver_id', 'rb.time', 'rb.bound'])
            ->groupBy('solver_id');

        foreach ($this->selected_solvers as $id) {
            $solver = $solvers[$id];
            $tmp = new DataPlot($solver->name . " " . $solver->version);
            $tmp->data = ($bounds[$id] ?? collect())
                ->map(fn($b) => [$b->time, $b->bound])
                ->all();
            $this->series[] = $tmp;
        }
    }

    public function render()
    {
        $this->create_data();
        return view('livewire.evolution');
    }
}
