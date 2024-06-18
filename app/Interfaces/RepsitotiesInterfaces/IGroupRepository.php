<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use Illuminate\Database\Eloquent\Collection;

interface IGroupRepository
{
    public function getGroupsForSelect(): Collection;
}
