<?php

namespace App\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class TableView extends Component
{
    public $evaluation;

    #[Reactive]
    public $filters;

    #[Reactive]
    public $selected_solvers;

    public $nb_benchmarks;


    public function mount($filters, $evaluation, $selected_solvers)
    {
        $this->filters = $filters;
        $this->selected_solvers = $selected_solvers;
        $this->evaluation = $evaluation;
    }

    public function render()
    {
        $this->nb_benchmarks = 0;
        foreach ($this->evaluation->benchmarks as $benchmark)
            if ($this->filters->is_filtered($benchmark) == false)
                $this->nb_benchmarks++;

        return view('livewire.table-view');
    }
}
