<?php

namespace App\Livewire\Versus;

use App\Misc\DataPlot;


class OneVsOneCop extends AbstractOneVsOne
{


    public function create_scatter()
    {
        $all_results = $this->evaluation->all_results_cop($this->filters->time_limit);


        $this->scatter = [];
        $this->scatter[] = new DataPlot("OPTIMUM");
        $this->scatter[] = new DataPlot("OPEN");

        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
            $type = $benchmark->get_type();
            $best_bound = $this->evaluation->best_bound_cop($benchmark->id, $type, [$this->solver_x, $this->solver_y], $this->filters->time_limit);

            $data_x = $all_results[$this->solver_x][$benchmark->id];
            $data_y = $all_results[$this->solver_y][$benchmark->id];
            $score_x = $this->evaluation->score_cop($benchmark->id, $this->solver_x, $type, $best_bound, $this->filters->time_limit);
            $score_y = $this->evaluation->score_cop($benchmark->id, $this->solver_y, $type, $best_bound, $this->filters->time_limit);

            if ($score_x->optimum && $score_y->optimum) {
                $this->scatter[0]->data[] = [$data_x->time, $data_y->time, $benchmark->name];
                continue;
            }
            if ($score_x->bb1 && $score_y->bb1) {
                $this->scatter[1]->data[] = [$data_x->bound_time, $data_y->bound_time, $benchmark->name];
                continue;
            }

            if ($score_x->optimum) {
                $this->scatter[0]->data[] = [$data_x->time, $this->filters->time_limit, $benchmark->name];
                continue;
            }
            if ($score_y->optimum) {
                $this->scatter[0]->data[] = [$this->filters->time_limit, $data_y->time, $benchmark->name];
                continue;
            }
            if ($score_x->bb1) {
                $this->scatter[1]->data[] = [$data_x->bound_time, $this->filters->time_limit, $benchmark->name];
                continue;
            }
            if ($score_y->bb1)
                $this->scatter[1]->data[] = [$this->filters->time_limit, $data_y->bound_time, $benchmark->name];
        }
        $this->dispatch('scatter-updated',
            series: array_map(fn($s) => ['name' => $s->name, 'data' => $s->data], $this->scatter),
        );
    }


    public function create_per_constraints()
    {


        $this->selected_constraints = [];
        $i = 0;
        foreach ($this->evaluation->constraints() as $constraints)
            $this->selected_constraints[$constraints] = $i++;

        $all_results = $this->evaluation->all_results_cop($this->filters->time_limit);


        $this->per_constraints["OPTX"] = new DataPlot($this->name_x . " OPTIMUM", array_fill(0, count($this->selected_constraints), 0), $this->name_x);
        $this->per_constraints["OPTY"] = new DataPlot($this->name_y . " OPTIMUM", array_fill(0, count($this->selected_constraints), 0), $this->name_y);
        $this->per_constraints["BB1X"] = new DataPlot($this->name_x . " BB1", array_fill(0, count($this->selected_constraints), 0), $this->name_x);
        $this->per_constraints["BB1Y"] = new DataPlot($this->name_y . " BB1", array_fill(0, count($this->selected_constraints), 0), $this->name_y);
        $this->per_constraints["BB2X"] = new DataPlot($this->name_x . " BB2", array_fill(0, count($this->selected_constraints), 0), $this->name_x);
        $this->per_constraints["BB2Y"] = new DataPlot($this->name_y . " BB2", array_fill(0, count($this->selected_constraints), 0), $this->name_y);

        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
            preg_match_all('/#(\w+):/', $benchmark->info_constraints, $matches);
            $constraints = $matches[1];
            $type = $benchmark->get_type();
            $best_bound = $this->evaluation->best_bound_cop($benchmark->id, $type, [$this->solver_x, $this->solver_y], $this->filters->time_limit);

            $score_x = $this->evaluation->score_cop($benchmark->id, $this->solver_x, $type, $best_bound, $this->filters->time_limit);
            $score_y = $this->evaluation->score_cop($benchmark->id, $this->solver_y, $type, $best_bound, $this->filters->time_limit);


            foreach ($constraints as $c) {
                $pos = $this->selected_constraints[$c];
                $this->per_constraints["OPTX"]->data[$pos] += $score_x->optimum;
                $this->per_constraints["BB1X"]->data[$pos] += $score_x->bb1;
                $this->per_constraints["BB2X"]->data[$pos] += $score_x->bb2;
                $this->per_constraints["OPTY"]->data[$pos] += $score_y->optimum;
                $this->per_constraints["BB1Y"]->data[$pos] += $score_y->bb1;
                $this->per_constraints["BB2Y"]->data[$pos] += $score_y->bb2;
            }
        }

        $categories = array_flip($this->selected_constraints); // pos => famille
        ksort($categories);
        $categories = array_values($categories); // liste ordonnée par position
        $this->dispatch('per_constraints-updated',
            series: array_values(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data, "group" => $s->group], $this->per_constraints)),
            selected_constraints: $categories,
        );
    }

    public function create_per_families()
    {
        $this->selected_families = [];
        $i = 0;
        foreach ($this->evaluation->families() as $family) {
            if (in_array($family, $this->filters->families))
                $this->selected_families[$family] = $i++;
        }


        $this->per_families["OPTX"] = new DataPlot($this->name_x . " OPTIMUM", array_fill(0, count($this->selected_families), 0), $this->name_x);
        $this->per_families["OPTY"] = new DataPlot($this->name_y . " OPTIMUM", array_fill(0, count($this->selected_families), 0), $this->name_y);
        $this->per_families["BB1X"] = new DataPlot($this->name_x . " BB1", array_fill(0, count($this->selected_families), 0), $this->name_x);
        $this->per_families["BB1Y"] = new DataPlot($this->name_y . " BB1", array_fill(0, count($this->selected_families), 0), $this->name_y);
        $this->per_families["BB2X"] = new DataPlot($this->name_x . " BB2", array_fill(0, count($this->selected_families), 0), $this->name_x);
        $this->per_families["BB2Y"] = new DataPlot($this->name_y . " BB2", array_fill(0, count($this->selected_families), 0), $this->name_y);

        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
            $type = $benchmark->get_type();
            $best_bound = $this->evaluation->best_bound_cop($benchmark->id, $type, [$this->solver_x, $this->solver_y], $this->filters->time_limit);
            $score_x = $this->evaluation->score_cop($benchmark->id, $this->solver_x, $type, $best_bound, $this->filters->time_limit);
            $score_y = $this->evaluation->score_cop($benchmark->id, $this->solver_y, $type, $best_bound, $this->filters->time_limit);

            $family = $benchmark->family;
            $pos = $this->selected_families[$family];
            $this->per_families["OPTX"]->data[$pos] += $score_x->optimum;
            $this->per_families["BB1X"]->data[$pos] += $score_x->bb1;
            $this->per_families["BB2X"]->data[$pos] += $score_x->bb2;
            $this->per_families["OPTY"]->data[$pos] += $score_y->optimum;
            $this->per_families["BB1Y"]->data[$pos] += $score_y->bb1;
            $this->per_families["BB2Y"]->data[$pos] += $score_y->bb2;
        }

        $categories = array_flip($this->selected_families); // pos => famille
        ksort($categories);
        $categories = array_values($categories); // liste ordonnée par position

        $this->dispatch('per_families-updated',
            series: array_values(array_map(fn($s) => ['name' => $s->name, 'data' => $s->data, "group" => $s->group], $this->per_families)),
            selected_families: $categories,
        );
    }
}
