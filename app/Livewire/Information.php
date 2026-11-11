<?php

namespace App\Livewire;

use App\Misc\Data;
use App\Misc\DataHeader;
use Livewire\Component;

class Information extends Component
{
    public $evaluation;
    public $data;
    public $header_information;
    public $search = "";

    public function create_data()
    {
        $this->data = [];
        foreach ($this->evaluation->benchmarks as $benchmark) {

            if ($this->search != "" && (str_contains($benchmark->family, $this->search) == false && str_contains($benchmark->info_constraints, $this->search) == false))
                continue;
            if (isset($this->data[$benchmark->family])) {
                $this->data[$benchmark->family][1]->value++;
                continue;
            }
            $this->data[$benchmark->family] = [new Data($benchmark->family, $c = "", $a = "", $vs = null, $t = "string"), new Data(1)];
            if ($benchmark->type != null)
                $this->data[$benchmark->family][] = new Data($benchmark->type, $c = "", $a = "", $vs = null, $t = "string");
            preg_match_all('/#(\w+):/', $benchmark->info_constraints, $matches);
            sort($matches[1]);
            $this->data[$benchmark->family][] = new Data(implode(" - ", $matches[1]), $c = "", $a = "", $vs = null, $t = "string");

        }


    }

    public function mount($evaluation)
    {
        $this->evaluation = $evaluation;
        if ($this->evaluation->type == "cop")
            $this->header_information = [new DataHeader("Family", "left"), new DataHeader("Number", "left"), new DataHeader("Optimisation", "left"), new DataHeader("Constraints", "left")];
        else
            $this->header_information = [new DataHeader("Family", "left"), new DataHeader("Number", "left"), new DataHeader("Constraints", "left")];;

    }

    public function render()
    {
        logger($this->search);
        $this->create_data();
        return view('livewire.information');
    }
}
