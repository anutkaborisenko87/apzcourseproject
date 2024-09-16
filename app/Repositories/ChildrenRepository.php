<?php

namespace App\Repositories;

use App\Exceptions\ChildrenControllerException;
use App\Interfaces\RepsitotiesInterfaces\ChildrenRepositoryInterface;
use App\Models\Children;
use App\Models\User;
use App\QueryFilters\ChildSearchBy;
use App\QueryFilters\ChildSortBy;
use App\QueryFilters\UserSearchBy;
use App\QueryFilters\UserSortBy;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ChildrenRepository implements ChildrenRepositoryInterface
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

    final public function getAllChildrenList(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatData(Children::query(), $request);
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    final public function getAllChildrenForEnrollment(Request $request): LengthAwarePaginator
    {
        try {
            return Children::whereNull('enrollment_date')
                ->whereNull('group_id')
                ->with('user')->paginate(10);
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    final public function getAllChildrenInTraining(Request $request): LengthAwarePaginator
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

    final public function getAllGraduatedChildren(Request $request): LengthAwarePaginator
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
    private function formatData($childrenQuery, Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);
        $users = app(Pipeline::class)
            ->send(User::query())
            ->through([
                UserSearchBy::class,
                UserSortBy::class,
            ])->thenReturn();
        $usersIds = $users->pluck('id')->toArray();
        $childrenList = $childrenQuery->whereHas('user', function ($query) use ($usersIds) {
            $query->whereIn('user_id', $usersIds);
        });
        $childrenList = app(Pipeline::class)
            ->send($childrenList)
            ->through([
                ChildSearchBy::class,
                ChildSortBy::class
            ])->thenReturn();
        if ($perPage !== 'all') {
            $childrenList =  $childrenList->paginate((int) $perPage);
        } else {
            $childrenList =  $childrenList->paginate($childrenList->count());
        }
        return $childrenList;
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
