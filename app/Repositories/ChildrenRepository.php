<?php

namespace App\Repositories;

use App\Exceptions\ChildrenControllerException;
use App\Interfaces\RepsitotiesInterfaces\IChildrenRepository;
use App\Models\Children;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ChildrenRepository implements IChildrenRepository
{

    final public function getChildrenForSelect(): Collection
    {
        try {
            $children = Children::with('user')->get();
            return $children;
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    final public function getChildrenForUpdateSelect(int $parrentId): Collection
    {
        try {
            $children = Children::with('user')->whereDoesntHave('parrent_relations', function ($query) use ($parrentId) {
                $query->where('parrent_id', $parrentId);
            })->get();
            return $children;
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    final public function getChildrenForGroupSelect(): Collection
    {
        try {
            $children = Children::with('user')
                ->whereNull('group_id')
                ->whereNull('graduation_date')
                ->get();
            return $children;
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    final public function getAllChildrenList(): LengthAwarePaginator
    {
        try {
            return Children::with('group')
                ->with('user')->paginate(10);
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    final public function getAllChildrenForEnrollment(): LengthAwarePaginator
    {
        try {
            return Children::whereNull('enrollment_date')
                ->whereNull('group_id')
                ->with('user')->paginate(10);
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    final public function getAllChildrenInTraining(): LengthAwarePaginator
    {
        try {
            $today = date('Y-m-d');
            return Children::whereNotNull('enrollment_date')
                ->whereNotNull('group_id')
                ->where(function ($query) use ($today) {
                    $query->whereDate('graduation_date', '<', $today);
                    $query->orWhereNull('graduation_date');
                })
                ->with('user')->paginate(10);
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    final public function getAllGraduatedChildren(): LengthAwarePaginator
    {
        try {
            $today = date('Y-m-d');
            return Children::whereNotNull('enrollment_date')
                ->whereDate('graduation_date', '>', $today)
                ->with('user')->paginate(10);
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    final public function getChildById(int $childId): Children
    {
        try {
            return Children::where('id', $childId)
                ->with('parrent_relations')
                ->with('group')
                ->with('user')->first();
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenNotFoundError($childId);
        }
    }

    final public function createChildInfo(array $data): Children
    {
        try {
            $relationsData = [];
            if (isset($data['parrents'])) {
                $relationsData = $data['parrents'];
                unset($data['parrents']);
            }
            $cteatedChild = Children::create($data);
            if (!$cteatedChild) throw new Exception('Помилка створення інформації про дитину');
            if (!empty($relationsData)) {
                $syncData = [];
                array_walk($relationsData, function ($item) use (&$syncData) {
                    $syncData[$item['parrent_id']] = ['relations' => $item['relations']];
                });
                $cteatedChild->parrent_relations()->sync($syncData);
            }
            return $cteatedChild;
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenNotCreatedError();
        }
    }

    final public function updateChildInfo(Children $child, array $data): Children
    {
        try {
            $relationsData = [];
            if (isset($data['parrents'])) {
                $relationsData = $data['parrents'];
                unset($data['parrents']);
            }
            if (!$child->update($data)) throw new Exception('Помилка створення інформації про дитину');
            $syncData = [];
            array_walk($relationsData, function ($item) use (&$syncData) {
                $syncData[$item['parrent_id']] = ['relations' => $item['relations']];
            });
            $child->parrent_relations()->sync($syncData);
            return $child;
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenNotUpdatedError();
        }
    }

    final public function  deleteChildInfo(Children $child): bool
    {
        try {
            if (!$child->delete())  throw ChildrenControllerException::childrenNotDeletedError($child->id);
            return true;
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenNotDeletedError($child->id);
        }
    }
}
