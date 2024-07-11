<?php

namespace App\QueryFilters;

use App\Models\Parrent;
use App\Traits\FormatSearchableFields;

class ParrentSearchBy extends UsersFilter
{
    use FormatSearchableFields;
    public function applyFilter($builder, $request)
    {
        $field = $request->input($this->filterName(), 'all');

        $searchTerm = $request->input('search_term', '');
        if ($searchTerm === '') {
            return $builder;
        }
        $fields = Parrent::getSearchableFields();

        return $this->formatFilter($builder, $field, $fields, $searchTerm);
    }
}
