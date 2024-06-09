<?php

namespace App\Repositories;

use App\Interfaces\RepsitotiesInterfaces\IRoleRepository;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class RoleRepository implements IRoleRepository
{

    final public function getRoles(): Collection
    {
        return Role::all();
    }
}
