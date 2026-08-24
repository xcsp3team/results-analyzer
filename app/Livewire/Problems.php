<?php

namespace App\Livewire;

use LivewireUI\Modal\ModalComponent;

class Problems extends ModalComponent {

    public $all_families;
    public $selected_families;

    public function mount($all_families, $selected_families)
    {
        $this->all_families = $all_families;
        $this->selected_families = $selected_families;
    }


    public function render()
    {
        return view('livewire.problems');
    }
}
