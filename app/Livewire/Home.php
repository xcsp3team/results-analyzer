<?php

namespace App\Livewire;

use App\Models\Evaluation;
use Livewire\Component;

class Home extends Component
{
    public $evaluations;
    public $evaluation = null;

    public function mount()
    {
        $this->evaluations = Evaluation::all();
        $this->evaluation = null;
    }

    public function render()
    {
        return view('livewire.home');
    }

    public function load()
    {
        $tmp = Evaluation::findOrFail($this->evaluation);
        $this->evaluation = null;
        $this->redirectRoute('evaluations', ['slug' => $tmp->slug]);
    }
}
