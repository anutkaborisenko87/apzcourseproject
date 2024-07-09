<?php

namespace App\QueryFilters;

use App\Models\User;

class SortBy extends UsersFilter
{
    function applyFilter($builder, $request)
    {
        $field = $request->input($this->filterName(), 'id');
        $sortableFields = User::getSortableFields();
        if ($field === 'user_id' || !in_array($field, $sortableFields)) {
            $field = 'id';
        }
        $direction = in_array($request->input('sort_direction', 'asc'), ['asc', 'desc']) ? $request->input('sort_direction', 'asc') : 'desc';
        $builder->orderBy($field, $direction);

        return $builder;
    }
}
