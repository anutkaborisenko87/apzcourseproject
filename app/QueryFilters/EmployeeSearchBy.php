<?php

namespace App\QueryFilters;

use App\Models\Employee;
use App\Models\User;
use App\Traits\FormatSearchableFields;
use Illuminate\Database\Eloquent\Builder;

class EmployeeSearchBy extends UsersFilter
{
    use FormatSearchableFields;
    public function applyFilter($builder, $request): Builder
    {
        $field = $request->input($this->filterName(), 'all');

        $searchTerm = $request->input('search_term', '');
        if ($searchTerm === '') {
            return $builder;
        }
        return $this->formatFilter($builder, $field, Employee::class, $searchTerm);
    }
}
