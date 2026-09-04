<?php

namespace App\Livewire\Summary;

use App\Misc\Data;
use App\Misc\DataHeader;

class SummaryCop extends AbstractSummary
{
    const int NAME = 0;
    const int SCORE = 1;
    const int OPTIMUM = 2;
    const int BB1 = 3;
    const int BB2 = 4;
    const int UNSAT = 5;
    const int UNSUPPORTED = 6;


    public function mount($filters, $evaluation, $selected_solvers)
    {
        parent::mount($filters, $evaluation, $selected_solvers);
        $this->header_summary = [
            new DataHeader("Solver", "left"),
            new DataHeader("Score"),
            new DataHeader("#Opt"),
            new DataHeader("#BB1"),
            new DataHeader("#BB2"),
            new DataHeader("#UNSAT"),
            new DataHeader("#Unsupported"),
        ];
    }


    public function create_summary()
    {
        $all_results = $this->evaluation->all_results_cop($this->filters->time_limit);
        $this->summary = [];
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $tmp = [];
            for ($i = 0; $i <= 6; $i++)
                $tmp[] = new Data();
            $tmp[self::NAME]->value = $selectedSolver->name . " " . $selectedSolver->version;
            foreach ($this->evaluation->benchmarks as $benchmark) {
                if ($this->filters->is_filtered($benchmark) || $benchmark->status == "UNKNOWN")
                    continue;
                $data = $all_results[$selectedSolver->id][$benchmark->id];
                if ($data->unsupported) {
                    $tmp[self::UNSUPPORTED]->value += 1;
                    continue;
                }
                if ($data->bug)
                    continue;
                if (str_contains(strtoupper($benchmark->type), "MAX"))
                    $type = "MAXIMIZE";
                else
                    $type = "MINIMIZE";

                $best_bound = $this->evaluation->best_bound_cop($benchmark->id, $type, $this->selected_solvers, $this->filters->time_limit);
                if ($data->status == "UNSAT" && $data->time < $this->filters->time_limit) {
                    $tmp[self::UNSAT]->value += 1;
                    continue;
                }
                if ($best_bound->bound === null)
                    continue;
                $score = $this->evaluation->score_cop($benchmark->id, $id, $type, $best_bound, $this->filters->time_limit);
                $tmp[self::OPTIMUM]->value += $score->optimum;
                $tmp[self::BB1]->value += $score->bb1;
                $tmp[self::BB2]->value += $score->bb2;
            }

            $tmp[self::SCORE]->value = $tmp[self::OPTIMUM]->value + $tmp[self::BB1]->value + fdiv($tmp[self::BB2]->value, 2) + $tmp[self::UNSAT]->value;
            $this->summary[] = $tmp;

        }
    }
}
