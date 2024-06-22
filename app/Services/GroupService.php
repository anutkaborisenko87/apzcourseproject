<?php

namespace App\Services;

use App\Http\Resources\GroupFullInfoResource;
use App\Http\Resources\GroupResource;
use App\Interfaces\ServicesInterfaces\IGroupService;
use App\Repositories\GroupRepository;
use Exception;

class GroupService implements IGroupService
{
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }
    final public function getGroupsListForSelect(): array
    {
        try {
            return GroupResource::collection($this->groupRepository->getGroupsForSelect())->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
    final public function getGroupsList(): array
    {
        try {
            return GroupFullInfoResource::collection($this->groupRepository->getGroupsList())->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function showGroupInfo(int $groupId, ?array $data = null): array
    {
        try {
            return (new GroupFullInfoResource($this->groupRepository->getGroupInfo($groupId, $data)))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function createNewGroup(array $data): array
    {
        try {
            return (new GroupFullInfoResource($this->groupRepository->addGroup($data)))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function updateGroup(int $groupId, array $data): array
    {
        try {
            $group = $this->groupRepository->getGroupById($groupId);
            return (new GroupFullInfoResource($this->groupRepository->updateGroup($group, $data)))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function deleteGroup(int $groupId): array
    {
        try {
            $group = $this->groupRepository->getGroupInfo($groupId);
            $groupResp = (new GroupFullInfoResource($group))->resolve();
            $this->groupRepository->deleteGroup($group);
            return $groupResp;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
