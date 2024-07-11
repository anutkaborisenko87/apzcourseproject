<?php

namespace App\QueryFilters;

use App\Models\Employee;
use App\Models\Parrent;
use App\Models\User;

class ParrentSortBy extends UsersFilter
{
    public function applyFilter($builder, $request)
    {
        $field = $request->input($this->filterName(), 'id');
        $userSortableFields = User::getSortableFields();
        $parrentSortableFields = Parrent::getSortableFields();
        if (!in_array($field, $userSortableFields) && !in_array($field, $parrentSortableFields)) {
            $field = 'id';
        }
        $direction = in_array($request->input('sort_direction', 'asc'), ['asc', 'desc']) ? $request->input('sort_direction', 'asc') : 'desc';
        if (in_array($field, $parrentSortableFields)) {
            $builder->orderBy($field, $direction);
        } else {
            $builder->leftJoin('users', 'parrents.user_id', '=', 'users.id')
                ->orderBy('users.' . $field, $direction);
        }


        return $builder;
    }
}
