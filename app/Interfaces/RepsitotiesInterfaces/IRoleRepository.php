<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use Illuminate\Database\Eloquent\Collection;

interface IRoleRepository
{
    public function getRoles(): Collection;
}
