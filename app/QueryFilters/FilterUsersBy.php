<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class FilterUsersBy extends UsersFilter
{

    public function applyFilter($builder, $request): Builder
    {
        $filter = $request->input($this->filterName());
        if (isset($filter['category']) && count($filter['category']) > 0) {
            $builder = $this->applyCategoryFilter($builder, $filter['category']);
        }
        if (isset($filter['birth_year']) && count($filter['birth_year']) > 0) {
            $builder = $this->applyBirthYearFilter($builder, $filter['birth_year']);
        }
        return $builder;
    }

    private function applyCategoryFilter(&$builder, array $filters)
    {
        $builder = $builder->when(count($filters) > 0, function ($query) use ($filters) {
            return $query->where(function ($query) use ($filters) {
                if (in_array('parents', $filters)) {
                    $query->orWhereHas('parrent');
                }
                if (in_array('children', $filters)) {
                    $query->orWhereHas('children');
                }
                if (in_array('employees', $filters)) {
                    $query->orWhereHas('employee');
                }
                if (in_array('users', $filters)) {
                    $query->orWhere(function ($query) {
                        $query->doesntHave('parrent')
                            ->doesntHave('children')
                            ->doesntHave('employee');
                    });
                }
            });
        });
        return $builder;
    }

    private function applyBirthYearFilter(&$builder, array $filters)
    {
        $builder = $builder->when(count($filters) > 0, function ($query) use ($filters) {
        return $query->where(function ($query) use ($filters) {
           array_walk($filters, function ($filter) use (&$query) {
              if ($filter === "null") {
                  $query->orWhereNull('birth_year');
              } else {
                  $query->orWhere('birth_year', $filter);
              }
           });
        });
    });
        return $builder;
    }
}
