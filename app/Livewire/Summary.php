<?php

namespace App\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class Summary extends Component {
    public $summary;
    public $header_summary;
    public $solvers;

    const int NAME = 0;
    const int TOTAL = 1;
    const int SAT = 2;
    const int UNSAT = 3;
    const int UNIQUE = 4;
    const int BEST = 5;
    const int UNSUPPORTED = 6;
    const int PAR2 = 7;

    public $evaluation;

    #[Reactive]
    public $filters;

    #[Reactive]
    public $selected_solvers;

    public function mount($filters, $evaluation, $selected_solvers)
    {
        $this->filters = $filters;
        $this->selected_solvers = $selected_solvers;
        $this->evaluation = $evaluation;
        foreach ($this->evaluation->solvers2 as $solver)
            $this->solvers[$solver->id] = $solver;

        $this->header_summary = [
            new DataHeader("Solver", "left"),
            new DataHeader("#Solved"),
            new DataHeader("#SAT"),
            new DataHeader("#UNSAT"),
            new DataHeader("#Exclusive"),
            new DataHeader("#Fastest"),
            new DataHeader("#Unsupported"),
            new DataHeader("#PAR2")
        ];
    }

    public function createSummary()
    {
        $this->summary = [];
        $all_results = $this->evaluation->all_results();

        // Nb solved
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $tmp = [];
            for ($i = 0; $i <= 7; $i++)
                $tmp[] = new Data();
            $tmp[self::NAME]->value = $selectedSolver->name . " " . $selectedSolver->version;
            foreach ($this->evaluation->benchmarks2 as $benchmark) {
                if ($this->filters->is_filtered($benchmark))
                    continue;
                $data = $all_results[$selectedSolver->id][$benchmark->id];
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
            if ($this->filters->is_filtered($benchmark))
                continue;
            $unique = -1;
            $j = 0;
            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                $data = $all_results[$selectedSolver->id][$benchmark->id];
                if ($data->bug)
                    continue;
                if ($data->time < $this->filters->time_limit)
                    $unique = ($unique == -1 ? $j : -2);
                $j++;
            }
            if ($unique >= 0)
                $this->summary[$unique][self::UNIQUE]->value++;

        }

        // VBS
        $vbs = [];
        for ($i = 0; $i <= 7; $i++)
            $vbs[] = new Data();
        $vbs[self::NAME]->value = "Virtual Best Solver";
        $vbs[self::NAME]->class = "italic";

        foreach ($this->evaluation->benchmarks2 as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
        $sat = false;
        $unsat = false;
        $tl = $this->evaluation->defaulttime;
        $unsupported = true;
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $data = $all_results[$selectedSolver->id][$benchmark->id];
            if($data->unsupported !== "UNSUPPORTED")
                $unsupported = false;
            if ($data->status === "SAT" && $data->time < $this->filters->time_limit && $data->bug === 0) {
                $sat = true;
                if ($tl > $data->time)
                    $tl = $data->time;
            }
            if ($data->status === "UNSAT" && $data->time < $this->filters->time_limit && $data->bug === 0) {
                $unsat = true;
                if ($tl > $data->time)
                    $tl = $data->time;
            }
        }

        if($unsupported) $vbs[self::UNSUPPORTED]++;
        if ($sat)  $vbs[self::SAT]->value++;
        if ($unsat)  $vbs[self::UNSAT]->value++;
        $vbs[self::PAR2]->value += ($sat || $unsat) ? $tl : ($this->filters->time_limit * 2);
        if ($sat || $unsat) $vbs[self::TOTAL]->value++;

        $position = 0;
        foreach($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $data = $all_results[$selectedSolver->id][$benchmark->id];
            if ($data->time === $tl && $tl !== $this->filters->time_limit && $data->bug === 0)
                $this->summary[$position][self::BEST]->value++;
            $position++;
        }

    }
    $vbs[self::BEST]->value = $vbs[self::TOTAL]->value;
    foreach($this->summary as $tmp)
        $vbs[self::UNIQUE]->value += $tmp[self::UNIQUE]->value;
    $this->summary[] = $vbs;
    }


    public function render()
    {
        $this->createSummary();
        return view('livewire.summary');
    }
}
