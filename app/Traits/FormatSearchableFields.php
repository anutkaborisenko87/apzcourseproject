<?php

namespace App\Traits;

trait FormatSearchableFields
{
    public function formatFilter(&$builder, string $field, array $fields, string $searchTerm)
    {
        if ($field === 'all') {
            $builder->where(function ($query) use ($field, $searchTerm, $fields) {
                array_walk($fields, function ($field) use (&$query, $searchTerm) {
                    $query->orWhere($field, 'LIKE', "%$searchTerm%");
                });
            });

        } else if (!in_array($field, $fields)) {
            return $builder->whereRaw('1 = 0');
        } else {
            $builder->where($field, 'LIKE', "%$searchTerm%");
        }
        return $builder;
    }
}
