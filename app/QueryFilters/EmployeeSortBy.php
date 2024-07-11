<?php

namespace App\QueryFilters;

use App\Models\Employee;
use App\Models\User;

class EmployeeSortBy extends UsersFilter
{
    public function applyFilter($builder, $request)
    {
        $field = $request->input($this->filterName(), 'id');
        $userSortableFields = User::getSortableFields();
        $emplotyeeSortableFields = Employee::getSortableFields();
        if (!in_array($field, $userSortableFields) && !in_array($field, $emplotyeeSortableFields)) {
            $field = 'id';
        }
        $direction = in_array($request->input('sort_direction', 'asc'), ['asc', 'desc']) ? $request->input('sort_direction', 'asc') : 'desc';
        if (in_array($field, $emplotyeeSortableFields)) {
            $builder->orderBy($field, $direction);
        } else {
            $builder->leftJoin('users', 'employees.user_id', '=', 'users.id')
                ->orderBy('users.' . $field, $direction);
        }


        return $builder;
    }
}
