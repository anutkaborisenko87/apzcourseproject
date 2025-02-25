<?php

namespace App\Traits;

use App\Models\User;

trait FormatSearchableFields
{
    public function formatFilter(&$builder,
                                 string $field,
                                 string $relationModelClass,
                                 string $searchTerm)
    {
        $relationalfields = $relationModelClass::getSearchableFields();
        $usersfields = User::getSearchableFields();
        $relationalTable = (new $relationModelClass())->getTable();
        $sql = $builder->toSql();

        if (strpos($sql, "left join `users` on `{$relationalTable}`.`user_id` = `users`.`id`") === false) {
            $builder->leftJoin('users', $relationalTable . '.user_id', '=', 'users.id');
        }
        if ($field === 'all') {
            $relationalConds = collect($relationalfields)->map(function($searcableField) use ($searchTerm, $relationalTable) {
                return "$relationalTable.$searcableField LIKE '%$searchTerm%'";
            })->implode(' OR ');

            $userConds = collect($usersfields)->map(function($searcableField) use ($searchTerm) {
                return "users.$searcableField LIKE '%$searchTerm%'";
            })->implode(' OR ');
            $builder->selectRaw("*, CASE WHEN ($relationalConds) OR ($userConds) THEN true else false end as founded");

        } else if ($field === 'user_name') {
            $searchTermsArr = explode(' ', $searchTerm);
            $fieldsForSearch = ['first_name', 'last_name', 'patronymic_name'];
            $selectConditions = collect($searchTermsArr)->map(function($term) use ($fieldsForSearch) {
                $groupedFields = collect($fieldsForSearch)->map(function($field) use ($term) {
                    return "users.$field LIKE '%$term%'";
                })->implode(' OR ');

                return "($groupedFields)";
            })->implode(' OR ');

            $builder->selectRaw("*, CASE WHEN $selectConditions THEN true else false end as founded");
        }  else if ($field === 'address') {
            $searchTermsArr = explode(' ', $searchTerm);
            $fieldsForSearch = ['city', 'street', 'house_number'];
            $selectConditions = collect($searchTermsArr)->map(function($term) use ($fieldsForSearch) {
                $groupedFields = collect($fieldsForSearch)->map(function($field) use ($term) {
                    return "users.$field LIKE '%$term%'";
                })->implode(' OR ');

                return "($groupedFields)";
            })->implode(' OR ');

            $builder->selectRaw("*, CASE WHEN $selectConditions THEN true else false end as founded");
        } else if (in_array($field, $usersfields)) {
            $selectRaw = "*, (CASE WHEN users.$field  LIKE '%$searchTerm%' THEN TRUE ELSE FALSE END) AS `founded`";
            $builder->selectRaw($selectRaw);
        } else if (!in_array($field, $relationalfields)) {
            return $builder->whereRaw('1 = 0');
        }  else if ($field === 'group') {
            $selectRaw = "*, (CASE WHEN groups.title  LIKE '%$searchTerm%' THEN TRUE ELSE FALSE END) AS `founded`";
            $builder ->leftJoin('groups', $relationalTable . '.group_id', '=', 'groups.id')
                ->selectRaw($selectRaw);
        } else {
            $selectRaw = "*, (CASE WHEN $field  LIKE '%$searchTerm%' THEN TRUE ELSE FALSE END) AS `founded`";
            $builder->selectRaw($selectRaw);
        }
        return $builder;
    }

}
