<?php

namespace App\Repositories;

use App\Interfaces\RepsitotiesInterfaces\IParrentsRepository;
use App\Models\Parrent;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ParrentsRepository implements IParrentsRepository
{

    final public function getActiveParrents(): LengthAwarePaginator
    {
        try {
            $parrents = Parrent::whereHas('user', function ($query) {
                $query->where('active', true);
            })
                ->with('children_relations')
                ->with('user')->paginate(10);
            return $parrents;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getActiveParrentsForSelect(): Collection
    {
        try {
            return Parrent::whereHas('user', function ($query) {
                $query->where('active', true);
            })->get();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getNotActiveParrents(): LengthAwarePaginator
    {
        try {
            return Parrent::whereHas('user', function ($query) {
                $query->where('active', false);
            })
                ->with('children_relations')
                ->with('user')->paginate(10);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getParrentById(int $id): ?Parrent
    {
        try {
            $parrent = Parrent::where('id', $id)
                ->with('children_relations')
                ->with('user')
                ->first();
            if (!$parrent) throw new Exception('Батька не знайдено');
            return $parrent;
        } catch (Exception $exception) {
            throw $exception;
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
            throw $exception;
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
            if (!$parrent->update($data)) throw new Exception('Помилка оновлення інформації про батька');
            return $parrent;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function deleteParrent(Parrent $parrent): bool
    {
        try {
            if (!$parrent->delete()) throw new Exception('Помилка видалення батька');
            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
