<?php

namespace App\Livewire\Details;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

abstract class AbstractDetails extends Component
{
    use WithPagination;


    protected $detailed_results = [];
    public $header_results = [];
    public $solvers;
    public $evaluation;
    public $instance_name = null;

    #[Reactive]
    public $filters;

    #[Reactive]
    public $selected_solvers;

    public $perPage;

    public $sort_field = 0;
    public $sort_direction = 'asc';
    public $page = 1;

    public function mount($filters, $evaluation, $selected_solvers)
    {
        $this->filters = $filters;
        $this->selected_solvers = $selected_solvers;
        $this->evaluation = $evaluation;
        foreach ($this->evaluation->solvers as $solver)
            $this->solvers[$solver->id] = $solver;
        if (Cookie::has('perPage'))
            $this->perPage = Cookie::get('perPage');
        else
            $this->perPage = 25;

    }

    public abstract function create_detailed_results();


    public function sort($field)
    {
        if ($this->sort_field === $field) {
            $this->sort_direction = $this->sort_direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_direction = 'asc';
            $this->sort_field = $field;
        }


    }

    public function gotoPage($page)
    {
        $this->page = $page;
    }

    public function nextPage()
    {
        $this->page += 1;
    }

    public function previousPage()
    {
        $this->page -= 1;
    }

    public function changePerPage()
    {
        $this->page = 1;
        Cookie::queue('perPage', $this->perPage, 60 * 24 * 365);;
    }

    public function export()
    {
        $this->create_detailed_results();
        $csvFileName = "data.csv";
        $csvFile = fopen($csvFileName, 'w');
        fputcsv($csvFile, array_map(fn($h) => $h->value, $this->header_results));
        foreach ($this->detailed_results as $row)
            fputcsv($csvFile, array_map(fn($h) => $h->value, $row));
        fclose($csvFile);
        Toaster::success("Data exported.");
        return response()->download($csvFileName)->deleteFileAfterSend();
    }

    public function render()
    {

        $this->create_detailed_results();
        usort($this->detailed_results, function ($row1, $row2) {
            if ($row1[$this->sort_field]->value_sort == null)
                $row1[$this->sort_field]->value_sort = $row1[$this->sort_field]->value;
            if ($row2[$this->sort_field]->value_sort == null)
                $row2[$this->sort_field]->value_sort = $row2[$this->sort_field]->value;

            if ($row1[$this->sort_field]->value_sort > $row2[$this->sort_field]->value_sort) {
                return $this->sort_direction === 'asc' ? 1 : -1;
            } elseif ($row1[$this->sort_field]->value_sort < $row2[$this->sort_field]->value_sort) {
                return $this->sort_direction === 'asc' ? -1 : 1;
            }
            return 0;
        });

        $page_results = collect($this->detailed_results)->forPage($this->page, $this->perPage)->values();

        $paginator_details = new LengthAwarePaginator(
            $page_results,
            count($this->detailed_results),
            $this->perPage,
            $this->page,
            ['pageName' => 'page']
        );

        return view('livewire.detailed', ['paginator_details' => $paginator_details]);
    }

}
