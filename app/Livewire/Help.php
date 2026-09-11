<?php

namespace App\Livewire;

use Livewire\Component;

class Help extends Component {
    public $type;

    public function mount($type)
    {
        $this->type = $type;
    }

    public function render()
    {
        return view('livewire.help');
    }
}
