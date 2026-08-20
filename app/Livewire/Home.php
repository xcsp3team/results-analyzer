<?php

namespace App\Livewire;

use App\Models\Competition;
use Livewire\Component;

class Home extends Component
{
    public $evaluations;
    public $evaluation = null;

    public function mount() {
        $this->evaluations = Competition::all();
        $this->evaluation = null;
    }

    public function render() {
        return view('livewire.home');
    }

    public function load() {
        $tmp = Competition::findOrFail($this->evaluation);
        $this->evaluation = null;
        if($tmp->type == "cop")
            $this->redirectRoute('evaluations_cop', ['slug' => $tmp->slug]);
        else
            $this->redirectRoute('evaluations', ['slug' => $tmp->slug]);
    }
}
