<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RolesService;
use Illuminate\Http\Response;

class RolesController extends Controller
{
    private RolesService $rolesService;
    public function __construct(RolesService $rolesService)
    {
        $this->rolesService = $rolesService;
    }

    final public function index(): Response
    {
        return response($this->rolesService->getRolesList());
    }
}
