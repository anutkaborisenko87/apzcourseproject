<?php

namespace App\Repositories;

use App\Interfaces\RepsitotiesInterfaces\IChildrenRepository;
use App\Models\Children;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ChildrenRepository implements IChildrenRepository
{

    final public function getChildrenForSelect(): Collection
    {
        try {
            $children = Children::whereHas('user', function ($query) {
                $query->where('active', true);
            })
                ->with('user')->get();
            return $children;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getChildrenForUpdateSelect(int $parrentId): Collection
    {
        try {
            $children = Children::whereHas('user', function ($query) use ($parrentId) {
                $query->where('active', true);
            })->whereDoesntHave('parrent_relations', function ($query) use ($parrentId) {
                $query->where('parrent_id', $parrentId);
            })->get();
            return $children;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
