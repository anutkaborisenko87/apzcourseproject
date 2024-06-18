<?php

namespace App\Repositories;

use App\Interfaces\RepsitotiesInterfaces\IGroupRepository;
use App\Models\Group;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class GroupRepository implements IGroupRepository
{

    final public function getGroupsForSelect(): Collection
    {
        try {
           return Group::all();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
