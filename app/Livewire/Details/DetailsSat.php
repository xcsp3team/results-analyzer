<?php

namespace App\Livewire\Details;

use App\Misc\Data;
use App\Misc\DataHeader;


class DetailsSat extends AbstractDetails
{

    public function create_detailed_results()
    {
        $all_results = $this->evaluation->all_results();
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

        foreach ($this->evaluation->benchmarks as $benchmark) {
            if ($this->filters->is_filtered($benchmark) || ($this->instance_name != null) && str_contains($benchmark->name, $this->instance_name) == false)
                continue;
            $name = $benchmark->name;
            $info = "#vars: $benchmark->nb_variables #ctrs: $benchmark->nb_constraints<br />";
            $info .= "domains &#8594; $benchmark->info_domains<br />";
            $info .= "constraints &#8594; $benchmark->info_constraints<br />";
            $tmp = [new Data($benchmark->name, "cursor-pointer", '@click="open=true;x=$event.clientX;y=$event.clientY;content={title:\'' . $name . '\',details:\'' . $info . '\'}"')];
            $tmp[] = new Data($benchmark->nb_variables);
            $tmp[] = new Data($benchmark->nb_constraints);
            $tmp[] = new Data($benchmark->status);

            $best = $this->filters->time_limit;
            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                if ($all_results[$selectedSolver->id][$benchmark->id]->time < $best)
                    $best = $all_results[$selectedSolver->id][$benchmark->id]->time;
            }

            foreach ($this->selected_solvers as $id) {
                $selectedSolver = $this->solvers[$id];
                $data = $all_results[$selectedSolver->id][$benchmark->id];
                if ($data->unsupported) {
                    $tmp[] = new Data("U", "", "", -2);
                    continue;
                }
                if ($data->bug) {
                    $tmp[] = new Data($data->time, "bg-red-500 opacity-50", "", -3);
                    continue;
                }
                if ($data->time <= $best && $data->time <= $this->filters->time_limit) {
                    $tmp[] = new Data((int)$data->time, "text-green-500");
                    continue;
                }
                if ($data->time <= $this->filters->time_limit)
                    $tmp[] = new Data((int)$data->time);
                else
                    $tmp[] = new Data("-", "opacity-30", "", -1);
            }
            $this->detailed_results[] = $tmp;
        }

    }

}
