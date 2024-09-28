<?php

namespace App\QueryFilters;

use App\Models\Employee;
use App\Traits\FormatSortableFieldsFilters;

class EmployeeSortBy extends UsersFilter
{
    use FormatSortableFieldsFilters;
    public function applyFilter($builder, $request)
    {
        $field = $request->input($this->filterName(), 'id');
        $direction = in_array($request->input('sort_direction', 'asc'), ['asc', 'desc']) ? $request->input('sort_direction', 'asc') : 'desc';

        return $this->formatSortableFilter($builder, $field, Employee::class, $direction);
    }
}
