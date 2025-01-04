<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class DateFilterUsersBy extends UsersFilter
{

    public function applyFilter($builder, $request): Builder
    {
        $filter = $request->input($this->filterName());

        if (isset($filter['birth_date']) && count($filter['birth_date']) > 0) {
            $builder = $this->applyBirthDateFilter($builder, $filter['birth_date']);
        }
        return $builder;
    }


    private function applyBirthDateFilter(&$builder, array $filters)
    {

        $builder = $builder->when(isset($filters['from']) || isset($filters['to']), function ($query) use ($filters) {
            return $query->where(function ($query) use ($filters) {
                array_walk($filters, function ($filter, $index) use (&$query) {
                    $symb = $index === 'from' ? '>=' : '<=';
                    $query->whereNotNull('birth_date')->whereDate('birth_date', $symb, $filter);
                });
            });
        });

        return $builder;
    }
}
