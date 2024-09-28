<?php

namespace App\QueryFilters;

use App\Models\Children;
use App\Traits\FormatSearchableFields;

class ChildSearchBy extends UsersFilter
{
    use FormatSearchableFields;
    public function applyFilter($builder, $request)
    {
        $field = $request->input($this->filterName(), 'all');

        $searchTerm = $request->input('search_term', '');
        if ($searchTerm === '') {
            return $builder;
        }

        return $this->formatFilter($builder, $field, Children::class, $searchTerm);
    }
}
