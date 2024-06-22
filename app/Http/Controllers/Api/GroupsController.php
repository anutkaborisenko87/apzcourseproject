<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GroupsRequests\CreateGroupRequest;
use App\Http\Requests\Api\GroupsRequests\ShowGroupInfoRequest;
use App\Http\Requests\Api\GroupsRequests\UpdateGroupRequest;
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
    final public function index(): Response
    {
        try {
            return response($this->groupService->getGroupsList());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
    final public function showGroupInfo(int $group): Response
    {
        try {
            return response($this->groupService->showGroupInfo($group));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
    final public function showFullGroupInfo(ShowGroupInfoRequest $request, int $group): Response
    {
        try {
            return response($this->groupService->showGroupInfo($group, $request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
    final public function storeGroupInfo(CreateGroupRequest $request): Response
    {
        try {
            return response($this->groupService->createNewGroup($request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
    final public function updateGroupInfo(UpdateGroupRequest $request, int $group): Response
    {
        try {
            return response($this->groupService->updateGroup($group, $request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
    final public function destroyGroupInfo(UpdateGroupRequest $request, int $group): Response
    {
        try {
            return response($this->groupService->updateGroup($group, $request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
}
