<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GroupsRequests\CreateGroupRequest;
use App\Http\Requests\Api\GroupsRequests\ShowGroupInfoRequest;
use App\Http\Requests\Api\GroupsRequests\UpdateGroupRequest;
use App\Interfaces\ServicesInterfaces\IGroupService;
use Illuminate\Http\Response;

class GroupsController extends Controller
{
    private IGroupService $groupService;

    public function __construct(IGroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    final public function indexSelect(): Response
    {
        return response($this->groupService->getGroupsListForSelect());
    }

    final public function index(): Response
    {
        return response($this->groupService->getGroupsList());
    }

    final public function showGroupInfo(int $group): Response
    {
        return response($this->groupService->showGroupInfo($group));
    }

    final public function showFullGroupInfo(ShowGroupInfoRequest $request, int $group): Response
    {
        return response($this->groupService->showGroupInfo($group, $request->validated()));
    }

    final public function storeGroupInfo(CreateGroupRequest $request): Response
    {
        return response($this->groupService->createNewGroup($request->validated()));
    }

    final public function updateGroupInfo(UpdateGroupRequest $request, int $group): Response
    {
        return response($this->groupService->updateGroup($group, $request->validated()));
    }

    final public function destroyGroupInfo(int $group): Response
    {
        return response($this->groupService->deleteGroup($group));
    }
}
