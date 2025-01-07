<?php

namespace App\Repositories;

use App\Exceptions\GroupsControllerException;
use App\Interfaces\RepsitotiesInterfaces\GroupRepositoryInterface;
use App\Models\Children;
use App\Models\Group;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class GroupRepository implements GroupRepositoryInterface
{
    /**
     * Retrieves all groups for populating a selection list.
     *
     * @return Collection Collection of groups.
     * @throws GroupsControllerException If an error occurs while fetching the group list.
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
     * Retrieves a list of groups with counts of enrolled children and assigned teachers.
     *
     * Children are counted if their enrollment date is on or before today and graduation
     * date has not passed or is not set. Teachers are counted if they are associated with
     * groups for the current date range.
     *
     * @return Collection Collection of groups with related counts.
     * @throws GroupsControllerException If an error occurs while fetching the group list.
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
                    $query->whereHas('groups', function ($q) use ($today) {
                        $q->where('date_start', '<=', $today)
                            ->where(function ($q) use ($today) {
                                $q->where('date_finish', '>=', $today)
                                    ->orWhereNull('date_finish');
                            });
                    });
                }
            ])->get();
        } catch (Exception $exception) {
            throw GroupsControllerException::getGroupsListError($exception->getCode() || Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Retrieves a group by its unique identifier.
     *
     * @param int $groupId The ID of the group to retrieve.
     * @return Group|null The group instance if found, or null if not found.
     * @throws GroupsControllerException If an error occurs while fetching the group or the group is not found.
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
     * Retrieves detailed group information by group ID, including optional filtered related data.
     *
     * If the optional data parameter is provided, related entities such as children, teachers,
     * and educational programs are loaded and filtered by the specified date range.
     *
     * @param int $groupId The ID of the group to retrieve information for.
     * @param array|null $data Optional array containing 'from' and 'to' dates for data filtering.
     *                          - 'from': The start date for filtering (inclusive).
     *                          - 'to': The end date for filtering (inclusive; defaults to the current date if not provided).
     *
     * @return Group|null The group with its related data, or null if not found.
     *
     * @throws GroupsControllerException If an error occurs while retrieving the group or its related data.
     */
    final public function getGroupInfo(int $groupId, ?array $data = null): ?Group
    {
        try {
            $month = now()->month;
            $year = now()->year;
            $group = $this->getGroupById($groupId);
            if ($month >= 9) {
                $from = $data['from'] ?? Carbon::create($year, 9, 1);
                $to = $data['to'] ?? Carbon::create($year + 5, 9, 1);
            } else {
                $from = $data['from'] ?? Carbon::create($year - 1, 9, 1);
                $to = $data['to'] ?? Carbon::create($year + 4, 9, 1);
            }
            $group->load([
                'children' => function ($query) use ($from, $to) {
                    $query->whereNotNull('enrollment_date')
                        ->where('enrollment_date', '>=', $from)
                        ->where('graduation_date', '<=', $to);
                },
                'teachers' => function ($query) use ($from, $to) {
                    $query->wherePivot('date_start', '>=', $from)
                        ->wherePivot('date_finish', '<=', $to);
                }
            ]);

            return $group;
        } catch (Exception $exception) {
            throw GroupsControllerException::getGroupsListError($exception->getCode());
        }
    }

    /**
     * Creates a new group and associates related entities such as children, teachers,
     * and educational programs based on the provided data.
     *
     * @param array $data Reference to an associative array containing group details
     *                    along with optional related entities like children, teachers,
     *                    and educational programs.
     * @return Group The newly created group instance with its relationships loaded.
     * @throws GroupsControllerException If an error occurs during group creation or
     *                                   association with related entities.
     */
    final public function addGroup(array &$data): Group
    {
        try {
            $children = [];
            if (!empty($data['children'])) {
                $children = $data['children'];
                unset($data['children']);
            }
            $dateStart = $data['date_start'] ?? null;
            $dateFinish = $data['date_finish'] ?? null;
            if (isset($data['date_start'])) unset($data['date_start']);
            if (isset($data['date_finish'])) unset($data['date_finish']);
            $teachersData = [];
            if (!empty($data['teachers'])) {
                $teachers = $data['teachers'];
                array_walk($teachers, function ($teacher) use (&$teachersData, $dateStart, $dateFinish) {
                    $teachersData[$teacher]['date_start'] = $dateStart ?? null;
                    $teachersData[$teacher]['date_finish'] = $dateFinish ?? null;
                });
                unset($data['teachers']);
            }
            $group = Group::create($data);
            if (!$group) throw GroupsControllerException::createGroupError(Response::HTTP_BAD_REQUEST);
            if (!empty($children)) {
                array_walk($children, function ($child) use ($group, $dateStart, $dateFinish) {
                    Children::where('id', $child)->update(['group_id' => $group->id, 'enrollment_date' => $dateStart, 'graduation_date' => $dateFinish]);
                });
            }
            if (!empty($teachersData)) {
                $group->teachers()->sync($teachersData);
            }
            return $group->load('children', 'teachers');
        } catch (Exception $exception) {
            throw GroupsControllerException::createGroupError(Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Updates the group with the provided data, including related entities like children, teachers,
     * and educational programs.
     *
     * @param Group $group The group to be updated.
     * @param array &$data Reference to the data array containing group updates, as well as
     *                     children, teachers, and educational programs details.
     *
     * @return Group The updated group with related entities loaded.
     *
     * @throws GroupsControllerException If an error occurs during the update operation.
     */
    final public function updateGroup(Group $group, array &$data): Group
    {
        try {
            $children = [];
            $dateStart = $data['date_start'] ?? null;
            $dateFinish = $data['date_finish'] ?? null;
            if (isset($data['date_start'])) unset($data['date_start']);
            if (isset($data['date_finish'])) unset($data['date_finish']);
            if (!empty($data['children'])) {
                $children = $data['children'];
                unset($data['children']);
            }
            $teachersData = [];
            if (!empty($data['teachers'])) {
                $teachers = $data['teachers'];
                array_walk($teachers, function ($teacher) use (&$teachersData, $dateStart, $dateFinish) {
                    $teachersData[$teacher]['date_start'] = $dateStart ?? null;
                    $teachersData[$teacher]['date_finish'] = $dateFinish ?? null;
                });
                unset($data['teachers']);
            }
            if (!empty($data)) {
                if (!$group->update($data)) throw GroupsControllerException::updateGroupError(Response::HTTP_BAD_REQUEST);
            }

            if (!empty($children)) {
                array_walk($children, function ($child) use ($group, $dateStart, $dateFinish) {
                    Children::where('id', $child)->update(['group_id' => $group->id, 'enrollment_date' => $dateStart, 'graduation_date' => $dateFinish]);
                });
            }
            if (!empty($teachersData)) {
                $group->teachers()->syncWithoutDetaching($teachersData);
            }
            return $group->load('children', 'teachers', 'educationalPrograms');
        } catch (Exception $exception) {
            throw GroupsControllerException::updateGroupError($exception->getCode());
        }
    }

    /**
     * Deletes the specified group.
     *
     * @param Group $group The group instance to be deleted.
     * @return bool True if the group was successfully deleted.
     * @throws GroupsControllerException If an error occurs during the deletion process.
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
