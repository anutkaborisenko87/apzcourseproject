<?php

namespace App\Services;

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
}
