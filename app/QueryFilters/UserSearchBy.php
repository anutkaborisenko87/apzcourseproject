<?php

namespace App\QueryFilters;

use App\Models\User;

class UserSearchBy extends UsersFilter
{

    public function applyFilter($builder, $request)
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

        } else if ($field === 'user_name') {
            $searchTermsArr = explode(' ', $searchTerm);
            $fieldsForSearch = ['first_name', 'last_name', 'patronymic_name'];
            array_walk($searchTermsArr, function ($searchTerm) use (&$builder, $fieldsForSearch) {
                $builder->where(function ($query) use ($searchTerm, $fieldsForSearch) {
                    array_walk($fieldsForSearch, function ($field) use (&$query, $searchTerm) {
                        $query->orWhere($field, 'LIKE', "%$searchTerm%");
                    });
                });
            });
        }  else if ($field === 'address') {
            $searchTermsArr = explode(' ', $searchTerm);
            $fieldsForSearch = ['city', 'street', 'house_number', 'apartment_number'];
            array_walk($searchTermsArr, function ($searchTerm) use (&$builder, $fieldsForSearch) {
                $builder->where(function ($query) use ($searchTerm, $fieldsForSearch) {
                    array_walk($fieldsForSearch, function ($field) use (&$query, $searchTerm) {
                        $query->orWhere($field, 'LIKE', "%$searchTerm%");
                    });
                });
            });
        }  else if (!in_array($field, $fields)) {
            return $builder->whereRaw('1 = 0');
        } else {
            $builder->where($field, 'LIKE', "%$searchTerm%");
        }
        return $builder;
    }
}
