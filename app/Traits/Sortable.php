<?php

namespace App\Traits;

trait Sortable
{
    public static function getSortableFields()
    {
        return array_merge(['id'], (new self())->getFillable());
    }

}
