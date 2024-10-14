<?php

namespace App\QueryFilters;

use App\Models\Parrent;
use App\Traits\FormatSearchableFields;
use Illuminate\Database\Eloquent\Builder;

class ParrentSearchBy extends UsersFilter
{
    use FormatSearchableFields;
    public function applyFilter($builder, $request): Builder
    {
        $field = $request->input($this->filterName(), 'all');

        $searchTerm = $request->input('search_term', '');
        if ($searchTerm === '') {
            return $builder;
        }

        return $this->formatFilter($builder, $field, Parrent::class, $searchTerm);
    }
}
