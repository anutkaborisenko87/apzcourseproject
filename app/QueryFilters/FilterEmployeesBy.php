<?php

namespace App\QueryFilters;

use App\Traits\HasJoinFiltersFields;
use Illuminate\Database\Eloquent\Builder;

class FilterEmployeesBy extends UsersFilter
{
    use HasJoinFiltersFields;

    public function applyFilter($builder, $request): Builder
    {
        $filters = $request->input($this->filterName());
        if (!empty($filters)) {
            $sql = $builder->toSql();
            if (strpos($sql, "left join `users` on `employees`.`user_id` = `users`.`id`") === false) {
                $builder = $builder->leftJoin('users', 'employees.user_id', '=', 'users.id');
            }
            if (!empty($filters['group'])) {
                $builder = $this->filterByGroup($builder, $filters['group']);
            }
            if (!empty($filters['city'])) {
                $builder = $this->getJoinFiltersBuilder($builder, 'city', $filters['city']);
            }
        }
        return $builder;
    }

    private function filterByGroup($builder, array $values)
    {
        $builder = $builder->whereHas('groups', function ($query) use ($values) {
            $query->where(function ($q) use ($values) {
                array_walk($values, function ($value) use (&$builder, $q) {
                    $q->orWhere('group_id', $value);
                });
            });

        });
        return $builder;
    }
}
