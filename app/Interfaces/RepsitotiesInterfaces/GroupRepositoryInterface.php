<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

interface GroupRepositoryInterface
{
    public function getGroupsForSelect(): Collection;
    public function getGroupsList(): Collection;
    public function getGroupById(int $groupId): ?Group;
    public function getGroupInfo(int $groupId, ?array $data): ?Group;
    public function addGroup(array &$data): Group;
    public function updateGroup(Group $group, array &$data): Group;
    public function deleteGroup(Group $group): bool;
}
