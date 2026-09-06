<?php

namespace App\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\Url;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Table extends Component
{

    public $_table;
    public $header;
    public $sticky;
    public $sort_field;

    public $sort_direction = 'asc';

    public function mount($table, $header, $sort_field, $sort_direction, $sticky = false)
    {
        $this->_table = $table;
        $this->header = $header;

        $this->sort_field = $sort_field;
        $this->sort_direction = $sort_direction;
        $this->sticky = $sticky;
        $this->sort($sort_field);
    }


    public function render()
    {

        $table = $this->_table;


        return view('livewire.table', ["table" => $table, "header" => $this->header]);
    }

    public function sort($field)
    {
        if ($this->sort_field === $field) {
            $this->sort_direction = $this->sort_direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_direction = 'asc';
            $this->sort_field = $field;
        }

        usort($this->_table, function ($row1, $row2) {
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

    }

    public function export()
    {
        $csvFileName = "data.csv";
        $csvFile = fopen($csvFileName, 'w');
        fputcsv($csvFile, array_map(fn($h) => $h->value, $this->header));
        foreach ($this->_table as $row)
            fputcsv($csvFile, array_map(fn($h) => $h->value, $row));
        fclose($csvFile);
        Toaster::success("Data exported.");
        return response()->download($csvFileName)->deleteFileAfterSend();
    }
}
