<?php

namespace App\Services;

use App\Http\Resources\RoleResource;
use App\Interfaces\ServicesInterfaces\IRolesService;
use App\Repositories\RoleRepository;

class RolesService implements IRolesService
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    final public function getRolesList(): array
    {
        return RoleResource::collection($this->roleRepository->getRoles())->resolve();
    }
}
