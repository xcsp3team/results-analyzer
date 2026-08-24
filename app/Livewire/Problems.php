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

    public function select($type)
    {
        if ($type == "all")
            $this->selected_families = $this->all_families;
        else
            $this->selected_families = [];
    }

    public function save()
    {
        $this->dispatch('filters_change', field: 'families', value: $this->selected_families);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.problems');
    }
}
