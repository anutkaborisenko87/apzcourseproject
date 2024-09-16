<?php

namespace App\QueryFilters;

use App\Models\Employee;
use App\Models\User;
use App\Traits\FormatSearchableFields;

class EmployeeSearchBy extends UsersFilter
{
    public function applyFilter($builder, $request)
    {
        $field = $request->input($this->filterName(), 'all');

        $searchTerm = $request->input('search_term', '');
        if ($searchTerm === '') {
            return $builder;
        }
        $employeesfields = Employee::getSearchableFields();
        $usersfields = User::getSearchableFields();
        if ($field === 'all') {
            $builder->leftJoin('users', 'employees.user_id', '=', 'users.id')
                ->where(function ($query) use ($field, $searchTerm, $employeesfields, $usersfields) {
                    array_walk($employeesfields, function ($field) use (&$query, $searchTerm) {
                        $query->orWhere('employees.' . $field, 'LIKE', "%$searchTerm%");
                    });
                    array_walk($usersfields, function ($field) use (&$query, $searchTerm) {
                        $query->orWhere('users.' . $field, 'LIKE', "%$searchTerm%");
                    });
                });
        } else if ($field === 'user_name') {
            $searchTermsArr = explode(' ', $searchTerm);
            $fieldsForSearch = ['first_name', 'last_name', 'patronymic_name'];
            array_walk($searchTermsArr, function ($searchTerm) use (&$builder, $fieldsForSearch) {
                $builder->leftJoin('users', 'employees.user_id', '=', 'users.id')
                    ->where(function ($query) use ($searchTerm, $fieldsForSearch) {
                        array_walk($fieldsForSearch, function ($field) use (&$query, $searchTerm) {
                            $query->orWhere('users.' . $field, 'LIKE', "%$searchTerm%");
                        });
                    });
            });
        } else if (in_array($field, $usersfields)) {
            $builder->leftJoin('users', 'employees.user_id', '=', 'users.id')
                ->where('users.' . $field, 'LIKE', "%$searchTerm%");
        } else if (!in_array($field, $employeesfields)) {
            return $builder->whereRaw('1 = 0');
        } else {
            $builder->where($field, 'LIKE', "%$searchTerm%");
        }
        return $builder;
    }
}
