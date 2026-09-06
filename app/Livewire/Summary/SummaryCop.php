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
        $vbs = [];
        for ($i = 0; $i <= 6; $i++) {
            $vbs[] = new Data();
            $vbs[count($vbs) - 1]->class = "bg-green-300 italic";

        }
        $vbs[self::NAME]->value = "Virtual Best Solver";
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $tmp = [];
            for ($i = 0; $i <= 6; $i++)
                $tmp[] = new Data();
            $tmp[self::NAME]->value = $selectedSolver->name . " " . $selectedSolver->version;
            $this->summary[] = $tmp;
        }


        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark) || $benchmark->status == "UNKNOWN")
                continue;
            $type = $benchmark->get_type();
            $i = 0;
            $unsat = 0;
            $optim = 0;
            $bb1 = 0;
            $unsupported = 1;
            foreach ($this->selected_solvers as $solver_id) {
                $data = $all_results[$solver_id][$benchmark->id];
                if ($data->unsupported) {
                    $this->summary[$i++][self::UNSUPPORTED]->value += 1;
                    continue;
                }
                if ($data->bug) {
                    $i++;
                    continue;
                }

                $best_bound = $this->evaluation->best_bound_cop($benchmark->id, $type, $this->selected_solvers, $this->filters->time_limit);
                if ($data->status == "UNSAT" && $data->time < $this->filters->time_limit) {
                    $this->summary[$i++][self::UNSAT]->value += 1;
                    $unsat = 1;
                    $unsupported = 0;
                    continue;
                }
                if ($best_bound->bound === null) {
                    $i++;
                    continue;
                }
                $score = $this->evaluation->score_cop($benchmark->id, $solver_id, $type, $best_bound, $this->filters->time_limit);
                $unsupported = 0;
                if ($score->optimum) {
                    $optim = 1;
                    $bb1 = 0;
                }
                if ($optim == 0 && $score->bb1)
                    $bb1 = 1;
                $this->summary[$i][self::OPTIMUM]->value += $score->optimum;
                $this->summary[$i][self::BB1]->value += $score->bb1;
                $this->summary[$i][self::BB2]->value += $score->bb2;
                $this->summary[$i][self::SCORE]->value = (float)$this->summary[$i][self::OPTIMUM]->value + $this->summary[$i][self::BB1]->value + fdiv($this->summary[$i][self::BB2]->value, 2) + $this->summary[$i][self::UNSAT]->value;
                $this->summary[$i][self::SCORE]->value_sort = (float)$this->summary[$i][self::SCORE]->value;
                $i++;
            }
            $vbs[self::UNSAT]->value += $unsat;
            $vbs[self::OPTIMUM]->value += $optim;
            $vbs[self::BB1]->value += $bb1;
            $vbs[self::UNSUPPORTED]->value += $unsupported;
        }
        $vbs[self::SCORE]->value = (float)($vbs[self::UNSAT]->value + $vbs[self::OPTIMUM]->value + $vbs[self::BB1]->value);
        $vbs[self::SCORE]->value_sort = (float)$vbs[self::SCORE]->value_sort;
        $this->summary[] = $vbs;
    }

}
