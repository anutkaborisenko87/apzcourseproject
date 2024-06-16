<?php

namespace App\Interfaces\RepsitotiesInterfaces;


use Illuminate\Database\Eloquent\Collection;

interface IChildrenRepository
{
    public function getChildrenForSelect(): Collection;
    public function getChildrenForUpdateSelect(int $parrentId): Collection;
}
