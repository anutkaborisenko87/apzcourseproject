<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasJoinFiltersFields
{
    public function getJoinFiltersBuilder(&$builder,
                                          string $fieldName,
                                          array $filters): Builder
    {
        $builder = $builder->where(function ($query) use ($filters, $fieldName) {
                array_walk($filters, function ($filter) use (&$query, $fieldName) {
                    if ( $filter !== 'null' && !is_null($filter)) {
                        $query->orWhere("users.$fieldName", $filter);
                    } else {
                        $query->orWhereNull("users.$fieldName");
                    }
                });
            });
        return $builder;
    }
}
