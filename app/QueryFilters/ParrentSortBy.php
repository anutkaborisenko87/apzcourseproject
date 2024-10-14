<?php

namespace App\QueryFilters;

use App\Models\Parrent;
use App\Traits\FormatSortableFieldsFilters;
use Illuminate\Database\Eloquent\Builder;

class ParrentSortBy extends UsersFilter
{
    use FormatSortableFieldsFilters;
    public function applyFilter($builder, $request): Builder
    {
        $field = $request->input($this->filterName(), 'id');
        $direction = in_array($request->input('sort_direction', 'asc'), ['asc', 'desc']) ? $request->input('sort_direction', 'asc') : 'desc';

        return  $this->formatSortableFilter($builder, $field, Parrent::class, $direction);
    }
}
