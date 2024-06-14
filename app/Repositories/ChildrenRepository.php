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
}
