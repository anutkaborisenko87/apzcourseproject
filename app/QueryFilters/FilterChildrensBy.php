<?php

namespace App\QueryFilters;

use App\Traits\HasJoinFiltersFields;
use Illuminate\Database\Eloquent\Builder;

class FilterChildrensBy extends UsersFilter
{
    use HasJoinFiltersFields;

    public function applyFilter($builder, $request): Builder
    {
        $filters = $request->input($this->filterName());
        if (!empty($filters)) {
            $sql = $builder->toSql();
            if (strpos($sql, "left join `users` on `childrens`.`user_id` = `users`.`id`") === false) {
                $builder = $builder->leftJoin('users', 'childrens.user_id', '=', 'users.id');
            }
            if (!empty($filters['group'])) {
                $builder = $this->filterByGroup($builder, $filters['group']);
            }
        }
        return $builder;
    }

    private function filterByGroup(Builder &$builder, array $groups): Builder
    {
        return $builder->whereIn('group_id', $groups);
    }

}
