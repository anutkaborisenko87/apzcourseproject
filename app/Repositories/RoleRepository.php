<?php

namespace App\Repositories;

use App\Interfaces\RepsitotiesInterfaces\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{

    final public function getRoles(): Collection
    {
        return Role::all();
    }
}
