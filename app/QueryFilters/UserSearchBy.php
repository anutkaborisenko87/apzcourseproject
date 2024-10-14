<?php

namespace App\QueryFilters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserSearchBy extends UsersFilter
{

    public function applyFilter($builder, $request): Builder
    {
        $field = $request->input($this->filterName(), 'all');

        $searchTerm = $request->input('search_term', '');
        if ($searchTerm === '') {
            return $builder;
        }
        $fields = User::getSearchableFields();

        if ($field === 'all') {
            $conditions = '';
            foreach ($fields as $field) {
                $conditions .= "$field LIKE '%$searchTerm%' OR ";
            }
            $conditions = rtrim($conditions, ' OR '); // Remove trailing ' OR'

            $selectRaw = "*, (CASE WHEN $conditions THEN TRUE ELSE FALSE END) AS `founded`";

            $builder->selectRaw($selectRaw);
        } else if ($field === 'user_name') {
            $searchTermsArr = explode(' ', $searchTerm);
            $fieldsForSearch = ['first_name', 'last_name', 'patronymic_name'];
            array_walk($searchTermsArr, function ($searchTerm) use (&$builder, $fieldsForSearch) {
                $conditions = '';
                foreach ($fieldsForSearch as $field) {
                    $conditions .= "$field LIKE '%$searchTerm%' OR ";
                }
                $conditions = rtrim($conditions, ' OR '); // Remove trailing ' OR'

                $selectRaw = "*, (CASE WHEN $conditions THEN TRUE ELSE FALSE END) AS `founded`";

                $builder->selectRaw($selectRaw);

            });
        } else if ($field === 'address') {
            $searchTermsArr = explode(' ', $searchTerm);
            $fieldsForSearch = ['city', 'street', 'house_number', 'apartment_number'];
            array_walk($searchTermsArr, function ($searchTerm) use (&$builder, $fieldsForSearch) {
                $conditions = '';
                foreach ($fieldsForSearch as $field) {
                    $conditions .= "$field LIKE '%$searchTerm%' OR ";
                }
                $conditions = rtrim($conditions, ' OR '); // Remove trailing ' OR'

                $selectRaw = "*, (CASE WHEN $conditions THEN TRUE ELSE FALSE END) AS `founded`";

                $builder->selectRaw($selectRaw);
            });
        }  else if (!in_array($field, $fields)) {
            return $builder->whereRaw('1 = 0');
        } else {
            $selectRaw = "*, (CASE WHEN $field  LIKE '%$searchTerm%' THEN TRUE ELSE FALSE END) AS `founded`";
            $builder->selectRaw($selectRaw);
        }
        return $builder;
    }
}
