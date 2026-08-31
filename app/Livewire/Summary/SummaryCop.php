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
            new DataHeader("#Opt"),
            new DataHeader("#BB1"),
            new DataHeader("#BB2"),
            new DataHeader("#UNSAT"),
            new DataHeader("#Unsupported"),
        ];
    }

    public function best_score($benchmark_id, $type)
    {
        $all_results = $this->evaluation->all_results_cop($this->filters->time_limit);
        $tmp = (object)["bound" => null, "optimum" => false];
        foreach ($this->selected_solvers as $id) {
            $selectedSolver = $this->solvers[$id];
            $data = $all_results[$selectedSolver->id][$benchmark_id];
        }
            return $tmp;
        }
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
                if ($this->filters->is_filtered($benchmark))
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

                $best_score = $this->best_score($benchmark->id, $type);
                if ($best_score->bound == null)
                    continue;
                if (($type == "MAXIMIZE" && $best_score->bound > $data->bound) || ($type == "MINIMIZE" && $best_score->bound < $data->bound))
                    continue;
                if ($data->time <= $this->filters->time_limit) {
                    $tmp[self::OPTIMUM]->value++;
                    continue;
                }
                if ($best_score->optimum == false)
                    $tmp[self::BB1]->value++;
                else $tmp[self::BB2]->value++;
            }
            $this->summary[] = $tmp;
        }
    }
}
