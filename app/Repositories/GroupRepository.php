<?php

namespace App\Repositories;

use App\Exceptions\GroupsControllerException;
use App\Interfaces\RepsitotiesInterfaces\GroupRepositoryInterface;
use App\Models\Children;
use App\Models\Group;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class GroupRepository implements GroupRepositoryInterface
{
    /**
     * @throws GroupsControllerException
     */
    final public function getGroupsForSelect(): Collection
    {
        try {
            return Group::all();
        } catch (Exception $exception) {
            throw GroupsControllerException::getGroupsListError($exception->getCode());
        }
    }

    /**
     * @throws GroupsControllerException
     */
    final public function getGroupsList(): Collection
    {
        try {
            $today = now()->format('Y-m-d');
            return Group::withCount([
                'children' => function ($query) use ($today) {
                    $query->where('enrollment_date', '<=', $today)
                        ->where(function ($q) use ($today) {
                            $q->orWhere('graduation_date', '>=', $today)
                                ->orWhereNull('graduation_date');
                        });
                },
                'teachers' => function ($query) use ($today) {
                    $query->whereHas('groups', function($q) use ($today) {
                        $q->where('date_start', '<=', $today)
                            ->where(function ($q) use ($today) {
                                $q->where('date_finish', '>=', $today)
                                    ->orWhereNull('date_finish');
                            });
                    });
                },
                'educationalPrograms' => function ($query) use ($today) {
                    $query->whereHas('groups', function($q) use ($today) {
                        $q->where('date_start', '<=', $today)
                            ->where(function ($q) use ($today) {
                                $q->where('date_finish', '>=', $today)
                                    ->orWhereNull('date_finish');
                            });
                    });
                },
            ])->get();
        } catch (Exception $exception) {
            throw GroupsControllerException::getGroupsListError($exception->getCode());
        }
    }

    /**
     * @throws GroupsControllerException
     */
    final public function getGroupById(int $groupId): ?Group
    {
        try {
            $group = Group::find($groupId);
            if (!$group) throw GroupsControllerException::getGroupByIdError($groupId);
            return $group;
        } catch (Exception $exception) {
            throw GroupsControllerException::getGroupByIdError($groupId);
        }
    }

    /**
     * @throws GroupsControllerException
     */
    final public function getGroupInfo(int $groupId, ?array $data = null): ?Group
    {
        try {
            $group = $this->getGroupById($groupId);
            if ($data) {
                $from = $data['from'] ?? null;
                $to = $data['to'] ?? date('Y-m-d');
                $group->load([
                    'children' => function ($query) use ($from, $to) {
                        $query->where('enrollment_date', '>=', $from)
                            ->where('graduation_date', '<=', $to);
                    },
                    'teachers' => function ($query) use ($from, $to) {
                        $query->wherePivot('date_start', '>=', $from)
                            ->wherePivot('date_finish', '<=', $to);
                    },
                    'educationalPrograms' => function ($query) use ($from, $to) {
                        $query->wherePivot('date_start', '>=', $from)
                            ->wherePivot('date_finish', '<=', $to);
                    },
                ]);
            }
            return $group;
        } catch (Exception $exception) {
            throw GroupsControllerException::getGroupsListError($exception->getCode());
        }
    }

    /**
     * @throws GroupsControllerException
     */
    final public function addGroup(array &$data): Group
    {
        try {
            $children = [];
            if (!empty($data['children'])) {
                $children = $data['children'];
                unset($data['children']);
            }
            $teachersData = [];
            if (!empty($data['teachers'])) {
                $teachers = $data['teachers'];
                array_walk($teachers, function ($teacher) use (&$teachersData) {
                    $teachersData[$teacher['employee_id']]['date_start'] = $teacher['date_start'] ?? null;
                    $teachersData[$teacher['employee_id']]['date_finish'] = $teacher['date_finish'] ?? null;
                });
                unset($data['teachers']);
            }
            $educationalProgramsData = [];
            if (!empty($data['educationalPrograms'])) {
                $educationalPrograms = $data['educationalPrograms'];
                array_walk($educationalPrograms, function ($teacher) use (&$educationalProgramsData) {
                    $educationalProgramsData[$teacher['ed_prog_id']]['date_start'] = $teacher['date_start'] ?? null;
                    $educationalProgramsData[$teacher['ed_prog_id']]['date_finish'] = $teacher['date_finish'] ?? null;
                });
                unset($data['educationalPrograms']);
            }
            $group = Group::create($data);
            if (!$group) throw GroupsControllerException::createGroupError(Response::HTTP_BAD_REQUEST);
            if (!empty($children)) {
                array_walk($children, function ($child) use ($group) {
                    Children::where('id', $child)->update(['group_id' => $group->id]);
                });
            }
            if (!empty($teachersData)) {
                $group->teachers()->sync($teachersData);
            }
            if (!empty($educationalProgramsData)) {
                $group->educationalPrograms()->sync($educationalProgramsData);
            }
            return $group->load('children', 'teachers', 'educationalPrograms');
        } catch (Exception $exception) {
            throw GroupsControllerException::createGroupError($exception->getCode());
        }
    }

    /**
     * @throws GroupsControllerException
     */
    final public function updateGroup(Group $group, array &$data): Group
    {
        try {
            $children = [];
            if (!empty($data['children'])) {
                $children = $data['children'];
                unset($data['children']);
            }
            $teachersData = [];
            if (!empty($data['teachers'])) {
                $teachers = $data['teachers'];
                array_walk($teachers, function ($teacher) use (&$teachersData) {
                    $teachersData[$teacher['employee_id']]['date_start'] = $teacher['date_start'] ?? null;
                    $teachersData[$teacher['employee_id']]['date_finish'] = $teacher['date_finish'] ?? null;
                });
                unset($data['teachers']);
            }
            $educationalProgramsData = [];
            if (!empty($data['educationalPrograms'])) {
                $educationalPrograms = $data['educationalPrograms'];
                array_walk($educationalPrograms, function ($teacher) use (&$educationalProgramsData) {
                    $educationalProgramsData[$teacher['ed_prog_id']]['date_start'] = $teacher['date_start'] ?? null;
                    $educationalProgramsData[$teacher['ed_prog_id']]['date_finish'] = $teacher['date_finish'] ?? null;
                });
                unset($data['educationalPrograms']);
            }
            if (!empty($data)) {
                if (!$group->update($data)) throw GroupsControllerException::updateGroupError(Response::HTTP_BAD_REQUEST);
            }

            if (!empty($children)) {
                array_walk($children, function ($child) use ($group) {
                    Children::where('id', $child)->update(['group_id' => $group->id]);
                });
            }
            if (!empty($teachersData)) {
                $group->teachers()->syncWithoutDetaching($teachersData);
            }
            if (!empty($educationalProgramsData)) {
                $group->educationalPrograms()->syncWithoutDetaching($educationalProgramsData);
            }
            return $group->load('children', 'teachers', 'educationalPrograms');
        } catch (Exception $exception) {
            throw GroupsControllerException::updateGroupError($exception->getCode());
        }
    }

    /**
     * @throws GroupsControllerException
     */
    final public function deleteGroup(Group $group): bool
    {
        try {
            if (!$group->delete()) throw GroupsControllerException::deleteGroupError($group->id);
            return true;
        } catch (Exception $exception) {
            throw GroupsControllerException::deleteGroupError($group->id);
        }
    }
}
