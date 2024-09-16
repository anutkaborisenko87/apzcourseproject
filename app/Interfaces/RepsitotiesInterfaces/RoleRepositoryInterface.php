<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function getRoles(): Collection;
}
