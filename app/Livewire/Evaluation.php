<?php

namespace App\Livewire;

use App\Models\Competition;
use Hamcrest\Core\Set;
use Livewire\Attributes\On;
use Livewire\Component;

class Evaluation extends Component {

    public $evaluation;

    public $summary;
    public $header_summary;
    public $selected_solvers = [];
    public $solvers = [];

    public $all_results = [];

    public $detailed_results = [];
    public $header_results = [];

    public Filters $filters;

    public function mount(string $slug)
    {
        $this->evaluation = Competition::where('slug', $slug)->firstOrFail();
        foreach ($this->evaluation->solvers2 as $solver) {
            $this->solvers[$solver->id] = $solver;
            $this->selected_solvers[$solver->id] = $solver->id;
        }
        $this->filters = new Filters($this->evaluation->defaulttime);
        $this->initialize();
        $this->createSummary();
        $this->createDetailedResults();
        $this->header_summary = [
            new DataHeader("Solver", "left"),
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

    public function createSummary()
    {
        $this->summary = [];


        // Nb solved
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $tmp = [];
            for ($i = 0; $i <= 7; $i++)
                $tmp[] = new Data();
            $tmp[self::NAME]->value = $selectedSolver->name . " " . $selectedSolver->version;
            foreach ($this->evaluation->benchmarks2 as $benchmark) {
                if ($this->isFiltered($benchmark))
                    continue;
                $data = $this->all_results[$selectedSolver->id][$benchmark->id];
                $tmp[self::UNSUPPORTED]->value += $data->unsupported;
                if ($data->bug == 0 && $data->time < $this->filters->time_limit) {
                    $tmp[self::TOTAL]->value++;
                    $tmp[self::SAT]->value += $data->status == "SAT" ? 1 : 0;
                    $tmp[self::UNSAT]->value += $data->status == "UNSAT" ? 1 : 0;
                    $tmp[self::PAR2]->value += $data->time;
                } else
                    $tmp[self::PAR2]->value += $this->filters->time_limit * 2;

            }
            $this->summary[] = $tmp;
        }

        // Unique
        $nbBenchmarks = 0;
        foreach ($this->evaluation->benchmarks2 as $benchmark) {
            if ($this->isFiltered($benchmark))
                continue;
            $unique = -1;
            $j = 0;
            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                $data = $this->all_results[$selectedSolver->id][$benchmark->id];
                if ($data->bug)
                    continue;
                if ($data->time < $this->filters->time_limit)
                    $unique = ($unique == -1 ? $j : -2);
                $j++;
            }
            if ($unique >= 0)
                $this->summary[$unique][self::UNIQUE]->value++;

        }
    }

    public function createDetailedResults()
    {
        $this->detailed_results = [];
        $this->header_results = [
            new DataHeader("Instance", "left"),
            new DataHeader("V"),
            new DataHeader("C"),
            new DataHeader("Status")
        ];
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $this->header_results[] = new DataHeader($selectedSolver->name . " " . $selectedSolver->version);
        }

        foreach ($this->evaluation->benchmarks2 as $benchmark) {
            if ($this->isFiltered($benchmark))
                continue;
            $tmp = [new Data($benchmark->name), new Data($benchmark->nb_variables), new Data($benchmark->nb_clauses), new Data($benchmark->status)];

            $best = $this->filters->time_limit;
            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                if ($this->all_results[$selectedSolver->id][$benchmark->id]->time < $best)
                    $best = $this->all_results[$selectedSolver->id][$benchmark->id]->time;
            }

            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                $data = $this->all_results[$selectedSolver->id][$benchmark->id];
                if ($data->unsupported) {
                    $tmp[] = new Data("U");
                    continue;
                }
                if ($data->bug) {
                    $tmp[] = new Data($data->time, "bug");
                    continue;
                }
                if ($data->time <= $best && $data->time <= $this->filters->time_limit) {
                    $tmp[] = new Data($data->time, "text.emerald-600");
                    continue;
                }
                if ($data->time <= $this->filters->time_limit)
                    $tmp[] = new Data($data->time);
                else
                    $tmp[] = new Data("-");
            }
            $this->detailed_results[] = $tmp;
        }
    }


    public function initialize()
    {
        foreach ($this->evaluation->solvers2 as $solver) {
            $tmp = $solver->results($this->evaluation);
            foreach ($tmp as $data) {
                $data->time = round($data->time);
                $this->all_results[$data->solver_id][$data->benchmark_id] = $data;
            }
        }
    }

    #[On('filters_change')]
    public function change_filtering($changes)
    {
        $f = $changes["field"];
        $v = $changes["value"];
        $this->filters->$f = $v;
        $this->createSummary();
        $this->createDetailedResults();
    }

    #[On('toggle_selected_solver')]
    public function toggle_selected_solver($id)
    {
        if (isset($this->selected_solvers[$id]))
            unset($this->selected_solvers[$id]);
        else
            $this->selected_solvers[$id] = $id;
        $this->createSummary();
        $this->createDetailedResults();
    }

    #[On("none-solvers")]
    public function none_solvers()
    {
        $this->selected_solvers = [];
        $this->createSummary();
        $this->createDetailedResults();
    }

    #[On("all-solvers")]
    public function all_solvers()
    {
        foreach ($this->evaluation->solvers2 as $solver)
            $this->selected_solvers[$solver->id] = $solver->id;
        $this->createSummary();
        $this->createDetailedResults();
    }


    public function isFiltered($benchmark)
    {
        return false;
    }


    public function render()
    {
        return view('livewire.evaluation');
    }
}
