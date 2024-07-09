<?php

namespace App\QueryFilters;

use App\Models\User;

class SearchBy extends UsersFilter
{

    function applyFilter($builder, $request)
    {
        $field = $request->input($this->filterName(), 'all');
        $searchTerm = $request->input('search_term', '');
        if ($searchTerm === '') {
            return $builder;
        }
        $fields = User::getSearchableFields();
        if ($field === 'all') {
            $builder->where(function ($query) use ($field, $searchTerm, $fields) {
                array_walk($fields, function ($field) use (&$query, $searchTerm) {
                    $query->orWhere($field, 'LIKE', "%$searchTerm%");
                });
            });

        } else if (!in_array($field, $fields)) {
            return $builder->whereRaw('1 = 0');
        } else {
            $builder->where($field, 'LIKE', "%$searchTerm%");
        }
        return $builder;
    }
}
