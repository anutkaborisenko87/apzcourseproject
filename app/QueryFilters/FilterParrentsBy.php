<?php

namespace App\QueryFilters;

use App\Traits\HasJoinFiltersFields;
use Illuminate\Database\Eloquent\Builder;

class FilterParrentsBy extends UsersFilter
{
    use HasJoinFiltersFields;

    public function applyFilter($builder, $request): Builder
    {
        $filters = $request->input($this->filterName());
        if (!empty($filters)) {
            $sql = $builder->toSql();
            if (strpos($sql, "left join `users` on `parrents`.`user_id` = `users`.`id`") === false) {
                $builder = $builder->leftJoin('users', 'parrents.user_id', '=', 'users.id');
            }
            if (!empty($filters['sex'])) {
                $builder = $this->getJoinFiltersBuilder($builder, 'sex', $filters['sex']);
            }
            if (!empty($filters['city'])) {
                $builder = $this->getJoinFiltersBuilder($builder, 'city', $filters['city']);
            }
            if (!empty($filters['marital_status'])) {
                $builder = $this->filterByMatrialStatus($builder, $filters['marital_status']);
            }
        }
        return $builder;
    }

    private function filterByMatrialStatus(Builder &$builder, array $marital_status): Builder
    {
        return $builder->where(function ($query) use ($marital_status) {
            array_walk($marital_status, function ($value) use (&$query) {
                if ($value !== 'without_status' && !is_null($value)) {
                    $query->orWhere('marital_status', $value);
                } else {
                    $query->orWhereNull('marital_status');
                }
            });
        });
    }

}
