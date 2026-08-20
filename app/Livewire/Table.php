<?php

namespace App\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\Url;
use Livewire\Component;

class Table extends Component {

    public $_table;
    public $header;

    public $sort_field;

    public $sort_direction = 'asc';

    public function mount($table, $header, $sort_field, $sort_direction)
    {
        $this->_table = $table;
        $this->header = $header;

        $this->sort_field = $sort_field;
        $this->sort_direction = $sort_direction;
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
            if ($row1[$this->sort_field] > $row2[$this->sort_field]) {
                return $this->sort_direction === 'asc' ? 1 : -1;
            } elseif ($row1[$this->sort_field] < $row2[$this->sort_field]) {
                return $this->sort_direction === 'asc' ? -1 : 1;
            }
            return 0;
        });

    }




}
