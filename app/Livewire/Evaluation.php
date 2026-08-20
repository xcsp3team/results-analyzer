<?php

namespace App\Livewire;

use App\Models\Competition;
use Livewire\Component;

class Evaluation extends Component
{

    public $evaluation;
    public $time_limit;

    public $summary;
    public $headerSummary;
    public $selectedSolvers;

    public $allResults = [];

    public $detailedResults = [];
    public $headerResults = [];

    public $filter_name = null;

    public $selectedBenchmarks;
    public function mount(string $slug) {
        $this->evaluation = Competition::where('slug', $slug)->firstOrFail();
        $this->selectedSolvers = $this->evaluation->solvers2;
        $this->time_limit = $this->evaluation->defaulttime;
        $this->initialize();
        $this->createSummary();
        $this->createDetailedResults();
        $this->headerSummary = [
            new DataHeader("Instance", "left"),
            new DataHeader("#Solved"),
            new DataHeader("#SAT"),
            new DataHeader("#UNSAT"),
            new DataHeader("#Exclusive"),
            new DataHeader("#Fastes"),
            new DataHeader("#UNSUPPORTED"),
            new DataHeader("#PAR2")
        ];
    }


    const int NAME = 0;
    const int TOTAL = 1;
    const int SAT = 2;
    const int UNSAT = 3;
    const int UNIQUE = 4;
    const int BEST = 5;
    const int UNSUPPORTED = 6;
    const int PAR2 = 7;

    public function createSummary() {
        $this->summary = [];


        // Nb solved
        foreach($this->selectedSolvers as $selectedSolver) {
            $tmp = [];
            for($i = 0; $i <= 7;$i++)
                $tmp[] = new Data();
            $tmp[self::NAME]->value = $selectedSolver->name . " " . $selectedSolver->version;
            foreach($this->evaluation->benchmarks2 as $benchmark) {
                if ($this->isFiltered($benchmark))
                    continue;
                $data = $this->allResults[$selectedSolver->id][$benchmark->id];
                $tmp[self::UNSUPPORTED]->value  += $data->unsupported;
                if($data->bug == 0 && $data->time < $this->time_limit) {
                    $tmp[self::TOTAL]->value++;
                    $tmp[self::SAT]->value += $data->status == "SAT" ? 1: 0;
                    $tmp[self::UNSAT]->value += $data->status == "UNSAT" ? 1: 0;
                    $tmp[self::PAR2]->value += $data->time;
                } else
                    $tmp[self::PAR2]->value += $this->time_limit * 2;

            }
            $this->summary[] = $tmp;
        }

        // Unique
        $nbBenchmarks = 0;
        foreach($this->evaluation->benchmarks2 as $benchmark) {
            if ($this->isFiltered($benchmark))
                continue;
            $unique = -1;
            $j = 0;
            foreach($this->selectedSolvers as $selectedSolver) {
                $data = $this->allResults[$selectedSolver->id][$benchmark->id];
                if($data->bug)
                    continue;
                if($data->time < $this->time_limit)
                    $unique = ($unique == -1 ? $j : -2);
                $j++;
            }
            if($unique >= 0)
                $this->summary[$unique][self::UNIQUE]->value++;

        }
    }

    public function createDetailedResults() {
        $this->detailedResults = [];
        $this->headerResults = [
            new DataHeader("Instance", "left"),
            new DataHeader("V" ),
            new DataHeader("C" ),
            new DataHeader("Status" )
        ];
        foreach($this->selectedSolvers as $selectedSolver)
            $this->headerResults[] = new DataHeader($selectedSolver->name . " " . $selectedSolver->version);

        foreach($this->evaluation->benchmarks2 as $benchmark) {
            if($this->isFiltered($benchmark))
                continue;
            $tmp = [new Data($benchmark->name), new Data($benchmark->nb_variables), new Data($benchmark->nb_clauses), new Data($benchmark->status)];

            $best = $this->time_limit;
            foreach($this->selectedSolvers as $selectedSolver) {
                if($this->allResults[$selectedSolver->id][$benchmark->id]->time < $best)
                    $best = $this->allResults[$selectedSolver->id][$benchmark->id]->time;
            }

            foreach($this->selectedSolvers as $selectedSolver) {
                $data = $this->allResults[$selectedSolver->id][$benchmark->id];
                if($data->unsupported) {
                    $tmp[] = new Data("U");
                    continue;
                }
                if($data->bug) {
                    $tmp[] = new Data($data->time, "bug");
                    continue;
                }
                if($data->time <= $best && $data->time <= $this->time_limit) {
                    $tmp[] = new Data($data->time, "text.emerald-600");
                    continue;
                }
                if($data->time <= $this->time_limit)
                    $tmp[] = new Data($data->time);
                else
                    $tmp[] = new Data("-");
            }
            $this->detailedResults[] = $tmp;
        }
    }


    public function initialize() {
        foreach($this->evaluation->solvers2 as $solver) {
            $tmp = $solver->results($this->evaluation);
                foreach($tmp as $data) {
                $data->time = round($data->time);
                $this->allResults[$data->solver_id][$data->benchmark_id] = $data;
            }
        }
    }

    public function change_filtering() {
        $this->createSummary();
        $this->createDetailedResults();
    }



    public function isFiltered($benchmark) {
        if($this->filter_name != null && str_contains($benchmark->name, $this->filter_name) == false)
            return true;
        return false;
    }

    public function render()
    {
        return view('livewire.evaluation');
    }
}
