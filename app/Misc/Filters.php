<?php

namespace App\Misc;

use Livewire\Wireable;

class Filters implements Wireable
{
    public $time_limit;
    public $status;
    public $type;
    public $families;
    public $constraints;
    public $are_forbidden;
    public $expression;
    public $category;
    public $enabled;


    public function __construct($category, $time_limit = 0, $status = "ALL", $families = [], $constraints = [], $are_forbidden = false, $expression = null, $type = "ALL", $enabled = false)
    {
        $this->time_limit = $time_limit;
        $this->status = $status;
        $this->families = $families;
        $this->constraints = $constraints;
        $this->are_forbidden = $are_forbidden;
        $this->expression = $expression;
        $this->type = $type;
        $this->category = $category;
        $this->enabled = $enabled;
    }

    public function toLivewire()
    {
        return [
            'category' => $this->category,
            'time_limit' => $this->time_limit,
            'status' => $this->status,
            'families' => $this->families,
            'constraints' => $this->constraints,
            'are_forbidden' => $this->are_forbidden,
            'expression' => $this->expression,
            'type' => $this->type,
            'enabled' => $this->enabled,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static($value['category'], $value['time_limit'], $value['status'], $value['families'], $value['constraints'], $value['are_forbidden'], $value['expression'], $value['type'], $value['enabled']);
    }

    public function is_filtered($benchmark)
    {
        if (in_array($benchmark->family, $this->families) == false)
            return true;

        if ($this->are_forbidden) {
            foreach ($this->constraints as $constraint) {
                if (str_contains($benchmark->info_constraints, $constraint) != false)
                    return true;
            }
        } else {
            foreach ($this->constraints as $constraint) {
                if (str_contains($benchmark->info_constraints, $constraint) == false)
                    return true;
            }
        }

        if ($this->expression != "d > 0 && v > 0 && c > 0") {
            $eval = new ExprEvaluator(['d' => $benchmark->max_degree(), 'c' => $benchmark->nb_constraints, 'v' => $benchmark->nb_variables]);
            try {
                if ($eval->evaluate($this->expression) == false)
                    return true;
            } catch (ExprEvaluatorException $e) {
                
            }
        }

        // SAT/CSP special filters
        if ($this->category == "sat") {
            if ($this->status == "UNSAT" && $benchmark->status != "UNSAT")
                return true;
            if ($this->status == "SAT" && $benchmark->status != "SAT")
                return true;
        }


        // COP special filters
        if ($this->category == "cop") {
            if ($this->status == "CLOSED" && ($benchmark->status == "SAT" || $benchmark->status == "UNKNOWN"))
                return true;
            if ($this->status == "OPEN" && ($benchmark->status == "OPTIMUM" || $benchmark->status == "UNSAT"))
                return true;


            if ($this->type == "MAXIMIZE" && $benchmark->get_type() != "MAXIMIZE")
                return true;
            if ($this->type == "MINIMIZE" && $benchmark->get_type() != "MINIMIZE")
                return true;
        }
        return false;
    }
}
