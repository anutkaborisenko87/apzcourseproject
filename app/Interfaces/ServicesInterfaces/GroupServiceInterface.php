<?php

namespace App\Interfaces\ServicesInterfaces;

interface GroupServiceInterface
{
    public function getGroupsListForSelect(): array;
    public function getGroupsList(): array;
    public function showGroupInfo(int $groupId, ?array $data): array;
    public function createNewGroup(array $data): array;
    public function updateGroup(int $groupId, array $data): array;
    public function deleteGroup(int $groupId): array;
}
