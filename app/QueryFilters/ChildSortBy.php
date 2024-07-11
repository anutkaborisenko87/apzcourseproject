<?php

namespace App\QueryFilters;

use App\Models\Children;
use App\Models\Employee;
use App\Models\User;

class ChildSortBy extends UsersFilter
{
    public function applyFilter($builder, $request)
    {
        $field = $request->input($this->filterName(), 'id');
        $userSortableFields = User::getSortableFields();
        $childSortableFields = Children::getSortableFields();
        if (!in_array($field, $userSortableFields) && !in_array($field, $childSortableFields)) {
            $field = 'id';
        }
        $direction = in_array($request->input('sort_direction', 'asc'), ['asc', 'desc']) ? $request->input('sort_direction', 'asc') : 'desc';
        if (in_array($field, $childSortableFields)) {
            $builder->orderBy($field, $direction);
        } else {
            $builder->leftJoin('users', 'childrens.user_id', '=', 'users.id')
                ->orderBy('users.' . $field, $direction);
        }


        return $builder;
    }
}
