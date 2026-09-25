<?php

namespace App\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class NavBar extends Component {
    public $title;

    public function render()
    {
        return view('livewire.nav-bar');
    }
}
