<?php

namespace App\Repositories;

use App\Exceptions\ParrentControllerException;
use App\Interfaces\RepsitotiesInterfaces\ParrentsRepositoryInterface;
use App\Models\Parrent;
use App\Models\User;
use App\QueryFilters\ParrentSearchBy;
use App\QueryFilters\ParrentSortBy;
use App\QueryFilters\UserSearchBy;
use App\QueryFilters\UserSortBy;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ParrentsRepository implements ParrentsRepositoryInterface
{

    final public function getActiveParrents(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatParrentsData (true, $request);
        } catch (Exception $exception) {
            throw ParrentControllerException::getActiveParrentsError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    private function formatParrentsData(bool $useractive, Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);
        $parrents = Parrent::whereHas('user', function ($query) use ($useractive) {
            $query->where('active', $useractive);
        })->with('user')->with('children_relations');
        $parrents = app(Pipeline::class)
            ->send($parrents)
            ->through([
                ParrentSearchBy::class,
                ParrentSortBy::class
            ])->thenReturn();

        if ($perPage !== 'all') {
            $parrents = $parrents->paginate((int)$perPage);
        } else {
            $parrents = $parrents->paginate($parrents->count());
        }
        return $parrents;
    }

    final public function getActiveParrentsForSelect(): Collection
    {
        try {
            return Parrent::whereHas('user', function ($query) {
                $query->where('active', true);
            })->get();
        } catch (Exception $exception) {
            throw ParrentControllerException::getActiveParrentsForSelectError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    final public function getActiveParrentsForUpdateSelect(int $childId): Collection
    {
        try {
            return Parrent::whereHas('user', function ($query) {
                $query->where('active', true);
            })->whereDoesntHave('children_relations', function ($query) use ($childId) {
                $query->where('child_id', $childId);
            })->get();
        } catch (Exception $exception) {
            throw ParrentControllerException::getActiveParrentsForSelectError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    final public function getNotActiveParrents(Request $request): LengthAwarePaginator
    {
        try {
            return  $this->formatParrentsData( false, $request);
        } catch (Exception $exception) {
            throw ParrentControllerException::getNotActiveParrentsError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    final public function getParrentById(int $id): ?Parrent
    {
        try {
            $parrent = Parrent::where('id', $id)
                ->with('children_relations')
                ->with('user')
                ->first();
            if (!$parrent) throw ParrentControllerException::parrentNotFoundError($id);
            return $parrent;
        } catch (Exception $exception) {
            throw ParrentControllerException::parrentNotFoundError($id);
        }
    }

    final public function createParrent(array $data): Parrent
    {
        try {
            $relationsData = [];
            if (isset($data['children'])) {
                $relationsData = $data['children'];
                unset($relationsData['children']);
            }
            $newParrent = Parrent::create($data);
            if (!$newParrent) throw new Exception('Помилка створення батька');
            if (!empty($relationsData)) {
                array_walk($relationsData, function ($item) use (&$relationsData, &$syncData) {
                    $syncData[$item['child_id']] = ['relations' => $item['relations']];
                });
                $newParrent->children_relations()->sync($syncData);
            }
            return $newParrent;
        } catch (Exception $exception) {
            throw ParrentControllerException::createParrentError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    final public function updateParrent(Parrent $parrent, array $data): Parrent
    {
        try {
            $syncData = [];
            if (isset($data['children'])) {
                $relationsData = $data['children'];
                array_walk($relationsData, function ($item) use (&$relationsData, &$syncData) {
                    $syncData[$item['child_id']] = ['relations' => $item['relations']];
                });
                unset($data['children']);
            }
            $parrent->children_relations()->sync($syncData);
            if (!$parrent->update($data)) throw ParrentControllerException::updateParrentError(Response::HTTP_BAD_REQUEST);
            return $parrent;
        } catch (Exception $exception) {
            throw ParrentControllerException::updateParrentError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    final public function deleteParrent(Parrent $parrent): bool
    {
        try {
            if (!$parrent->delete()) throw ParrentControllerException::deleteParrentError($parrent->id);
            return true;
        } catch (Exception $exception) {
            throw ParrentControllerException::deleteParrentError($parrent->id);
        }
    }
}
