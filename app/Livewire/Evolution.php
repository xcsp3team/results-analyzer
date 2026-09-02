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
        foreach ($this->selected_solvers as $id) {
            $solver = Solver::find($id);
            $tmp = new DataPlot($solver->name . " " . $solver->version);
            $results = DB::select("SELECT * FROM results where benchmark_id=? and solver_id=?", [$this->benchmark_id, $id])[0];
            $bounds = json_decode(str_replace("'", '"', $results->bounds));
            $values = [];
            foreach ($bounds as $b)
                if ($b->time <= $this->time_limit)
                    $values[] = $b->bound;
                else break;
            $tmp->data = $values;
            $this->series[] = $tmp;
        }
    }

    public function render()
    {
        $this->create_data();
        return view('livewire.evolution');
    }
}
