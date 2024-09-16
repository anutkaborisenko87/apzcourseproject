<?php

namespace App\Services;

use App\Http\Resources\GroupFullInfoResource;
use App\Http\Resources\GroupResource;
use App\Interfaces\ServicesInterfaces\GroupServiceInterface;
use App\Interfaces\RepsitotiesInterfaces\GroupRepositoryInterface;

class GroupService implements GroupServiceInterface
{
    private GroupRepositoryInterface $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    final public function getGroupsListForSelect(): array
    {
        return GroupResource::collection($this->groupRepository->getGroupsForSelect())->resolve();
    }

    final public function getGroupsList(): array
    {
        return GroupFullInfoResource::collection($this->groupRepository->getGroupsList())->resolve();
    }

    final public function showGroupInfo(int $groupId, ?array $data = null): array
    {
        return (new GroupFullInfoResource($this->groupRepository->getGroupInfo($groupId, $data)))->resolve();
    }

    final public function createNewGroup(array $data): array
    {
        return (new GroupFullInfoResource($this->groupRepository->addGroup($data)))->resolve();
    }

    final public function updateGroup(int $groupId, array $data): array
    {
        $group = $this->groupRepository->getGroupById($groupId);
        return (new GroupFullInfoResource($this->groupRepository->updateGroup($group, $data)))->resolve();
    }

    final public function deleteGroup(int $groupId): array
    {
        $group = $this->groupRepository->getGroupInfo($groupId);
        $groupResp = (new GroupFullInfoResource($group))->resolve();
        $this->groupRepository->deleteGroup($group);
        return $groupResp;
    }
}
