<?php

namespace App\Livewire;

use LivewireUI\Modal\ModalComponent;

class Constraints extends ModalComponent {

    public $all_constraints;
    public $selected_constraints;
    public $forbidden;

    public function mount($all_constraints, $selected_constraints, $forbidden)
    {
        $this->all_constraints = $all_constraints;
        $this->selected_constraints = $selected_constraints;
        $this->forbidden = $forbidden;
    }


    public function save()
    {
        $this->dispatch('filters_change', field: 'constraints', value: $this->selected_constraints, forbidden: $this->forbidden);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.constraints');
    }
}
