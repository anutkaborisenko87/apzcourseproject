<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GroupService;
use Exception;
use Illuminate\Http\Response;

class GroupsController extends Controller
{
    private $groupService;

    public function __construct(GroupService $groupService)
    {
         $this->groupService = $groupService;
    }

    final public function indexSelect(): Response
    {
        try {
            return response($this->groupService->getGroupsListForSelect());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
}
