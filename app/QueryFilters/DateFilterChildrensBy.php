<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class DateFilterChildrensBy extends UsersFilter
{

    public function applyFilter($builder, $request): Builder
    {
        $filter = $request->input($this->filterName());

        if (isset($filter['enrollment_date']) && count($filter['enrollment_date']) > 0) {
            $builder = $this->applyEnrollmentGraduationDateFilter($builder, $filter['enrollment_date'], 'enrollment_date');
        }
        if (isset($filter['graduation_date']) && count($filter['graduation_date']) > 0) {
            $builder = $this->applyEnrollmentGraduationDateFilter($builder, $filter['graduation_date'], 'graduation_date');
        }
        if (isset($filter['birth_date']) && count($filter['birth_date']) > 0) {
            $builder = $this->applyBirthDateFilter($builder, $filter['birth_date']);
        }
        return $builder;
    }

    private function applyEnrollmentGraduationDateFilter(&$builder, array $filters, string $column)
    {

        $builder = $builder->when(isset($filters['from']) || isset($filters['to']), function ($query) use ($filters, $column) {
            return $query->where(function ($query) use ($filters, $column) {
                array_walk($filters, function ($filter, $index) use (&$query, $column) {
                    $symb = $index === 'from' ? '>=' : '<=';
                    $query->whereNotNull($column)->whereDate($column, $symb, $filter);
                });
            });
        });

        return $builder;
    }

    private function applyBirthDateFilter(&$builder, array $filters)
    {

        $builder = $builder->when(isset($filters['from']) || isset($filters['to']), function ($query) use ($filters) {
            return $query->whereHas('user', function ($query) use ($filters) {
                array_walk($filters, function ($filter, $index) use (&$query) {
                    $symb = $index === 'from' ? '>=' : '<=';
                    $query->whereNotNull('birth_date')->whereDate('birth_date', $symb, $filter);
                });
            });
        });
        return $builder;
    }
}
