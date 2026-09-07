<?php

namespace App\Livewire\Versus;

use App\Misc\DataPlot;


class OneVsOneCop extends AbstractOneVsOne
{


    public function create_scatter()
    {
        $all_results = $this->evaluation->all_results_cop($this->filters->time_limit);


        $this->scatter = [];
        $this->scatter[] = new DataPlot("OPT");
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


        $this->per_constraints["SATX"] = new DataPlot($this->name_x . " SAT", array_fill(0, count($this->selected_constraints), 0), $this->name_x);
        $this->per_constraints["SATY"] = new DataPlot($this->name_y . " SAT", array_fill(0, count($this->selected_constraints), 0), $this->name_y);
        $this->per_constraints["UNSATX"] = new DataPlot($this->name_x . " UNSAT", array_fill(0, count($this->selected_constraints), 0), $this->name_x);
        $this->per_constraints["UNSATY"] = new DataPlot($this->name_y . " UNSAT", array_fill(0, count($this->selected_constraints), 0), $this->name_y);

        $all_results = $this->evaluation->all_results();
        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
            preg_match_all('/#(\w+):/', $benchmark->info_constraints, $matches);
            $constraints = $matches[1];

            foreach ($constraints as $c) {
                $pos = $this->selected_constraints[$c];
                $data_x = $all_results[$this->solver_x][$benchmark->id];
                $data_y = $all_results[$this->solver_y][$benchmark->id];
                if ($data_x->time <= $this->filters->time_limit && $data_x->bug == false) {
                    if ($benchmark->status == "SAT")
                        $this->per_constraints["SATX"]->data[$pos]++;
                    else
                        $this->per_constraints["UNSATX"]->data[$pos]++;
                }
                if ($data_y->time <= $this->filters->time_limit && $data_y->bug == false) {
                    if ($benchmark->status == "SAT")
                        $this->per_constraints["SATY"]->data[$pos]++;
                    else
                        $this->per_constraints["UNSATY"]->data[$pos]++;
                }
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

        $this->per_families["SATX"] = new DataPlot($this->name_x . " SAT", array_fill(0, count($this->selected_families), 0), $this->name_x);
        $this->per_families["SATY"] = new DataPlot($this->name_y . " SAT", array_fill(0, count($this->selected_families), 0), $this->name_y);
        $this->per_families["UNSATX"] = new DataPlot($this->name_x . " UNSAT", array_fill(0, count($this->selected_families), 0), $this->name_x);
        $this->per_families["UNSATY"] = new DataPlot($this->name_y . " UNSAT", array_fill(0, count($this->selected_families), 0), $this->name_y);

        $all_results = $this->evaluation->all_results();
        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark))
                continue;
            $family = $benchmark->family;
            $pos = $this->selected_families[$family];
            $data_x = $all_results[$this->solver_x][$benchmark->id];
            $data_y = $all_results[$this->solver_y][$benchmark->id];
            if ($data_x->time <= $this->filters->time_limit && $data_x->bug == false) {
                if ($benchmark->status == "SAT")
                    $this->per_families["SATX"]->data[$pos]++;
                else
                    $this->per_families["UNSATX"]->data[$pos]++;
            }
            if ($data_y->time <= $this->filters->time_limit && $data_y->bug == false) {
                if ($benchmark->status == "SAT")
                    $this->per_families["SATY"]->data[$pos]++;
                else
                    $this->per_families["UNSATY"]->data[$pos]++;
            }
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
