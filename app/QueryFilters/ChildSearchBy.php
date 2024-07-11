<?php

namespace App\QueryFilters;

use App\Models\Children;
use App\Models\Employee;
use App\Traits\FormatSearchableFields;

class ChildSearchBy extends UsersFilter
{
    public function applyFilter($builder, $request)
    {
        $field = $request->input($this->filterName(), 'all');

        $searchTerm = $request->input('search_term', '');
        if ($searchTerm === '') {
            return $builder;
        }
        $fields = Children::getSearchableFields();
        if ($field === 'all') {
            $builder->where(function ($query) use ($field, $searchTerm, $fields) {
                array_walk($fields, function ($field) use (&$query, $searchTerm) {
                    if ($field !== 'group') {
                        $query->orWhere($field, 'LIKE', "%$searchTerm%");
                    }
                });
            });
            $builder->orWhereHas('group', function ($query) use ($searchTerm) {
                $query->where('title', 'LIKE', "%$searchTerm%");
            });

        } else if ($field === 'group') {
            return $builder->whereHas('group', function ($query) use ($searchTerm) {
                $query->where('title', 'LIKE', "%$searchTerm%");
            });
        } else if (!in_array($field, $fields)) {
            return $builder->whereRaw('1 = 0');
        } else {
            $builder->where($field, 'LIKE', "%$searchTerm%");
        }
        dd($builder);

        return $builder;
    }
}
