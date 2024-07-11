<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use App\Models\Parrent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface IParrentsRepository
{
    public function getActiveParrents(Request $request): LengthAwarePaginator;
    public function getActiveParrentsForSelect(): Collection;
    public function getActiveParrentsForUpdateSelect(int $childId): Collection;
    public function getNotActiveParrents(Request $request): LengthAwarePaginator;
    public function getParrentById(int $id): ?Parrent;
    public function createParrent(array $data): Parrent;
    public function updateParrent(Parrent $parrent, array $data): Parrent;
    public function deleteParrent(Parrent $parrent): bool;

}
