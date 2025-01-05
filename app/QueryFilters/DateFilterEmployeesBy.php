<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class DateFilterEmployeesBy extends UsersFilter
{

    public function applyFilter($builder, $request): Builder
    {
        $filter = $request->input($this->filterName());

        if (isset($filter['employment_date']) && count($filter['employment_date']) > 0) {
            $builder = $this->applyEmploymentDateFilter($builder, $filter['employment_date']);
        }
        return $builder;
    }


    private function applyEmploymentDateFilter(&$builder, array $filters)
    {

        $builder = $builder->when(isset($filters['from']) || isset($filters['to']), function ($query) use ($filters) {
            return $query->where(function ($query) use ($filters) {
                array_walk($filters, function ($filter, $index) use (&$query) {
                    $symb = $index === 'from' ? '>=' : '<=';
                    $query->whereNotNull('employment_date')->whereDate('employment_date', $symb, $filter);
                });
            });
        });

        return $builder;
    }
}
