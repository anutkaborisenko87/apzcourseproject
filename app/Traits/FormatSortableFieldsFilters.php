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
        $sql = $builder->toSql();
        if (strpos($sql, "left join `users` on `{$relationalTable}`.`user_id` = `users`.`id`") === false) {
            $builder->leftJoin('users', $relationalTable . '.user_id', '=', 'users.id');
        }
        if (!in_array($field, $userSortableFields) && !in_array($field, $relationalSortableFields)) {
            $field = 'id';
        }
        if (in_array($field, $relationalSortableFields)) {
            $builder->orderBy($field, $direction);
        } else {
            $builder->orderBy('users.' . $field, $direction);
        }
        return $builder;
    }

}
