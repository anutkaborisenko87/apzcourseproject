<?php

namespace App\Repositories;

use App\Interfaces\RepsitotiesInterfaces\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    /**
     * Retrieve all roles from the database.
     *
     * @return Collection
     */
    final public function getRoles(): Collection
    {
        return Role::all();
    }
}
