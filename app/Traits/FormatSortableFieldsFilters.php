<?php

namespace App\Traits;

use App\Models\User;

trait FormatSortableFieldsFilters
{
    public function formatSortableFilter(&$builder,
                                         string $field,
                                         string $modelClass,
                                         string $direction
    )
    {
        $userSortableFields = User::getSortableFields();
        $relationalSortableFields = $modelClass::getSortableFields();
        $relationalTable = (new $modelClass())->getTable();
        if (!in_array($field, $userSortableFields) && !in_array($field, $relationalSortableFields)) {
            $field = 'id';
        }
        if (in_array($field, $relationalSortableFields)) {
            $builder->orderBy($field, $direction);
        } else {
            $builder->leftJoin('users', $relationalTable . '.user_id', '=', 'users.id')
                ->orderBy('users.' . $field, $direction);
        }
        return $builder;
    }

}
