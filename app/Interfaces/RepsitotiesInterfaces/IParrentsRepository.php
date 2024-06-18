<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use App\Models\Parrent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface IParrentsRepository
{
    public function getActiveParrents(): LengthAwarePaginator;
    public function getActiveParrentsForSelect(): Collection;
    public function getActiveParrentsForUpdateSelect(int $childId): Collection;
    public function getNotActiveParrents(): LengthAwarePaginator;
    public function getParrentById(int $id): ?Parrent;
    public function createParrent(array $data): Parrent;
    public function updateParrent(Parrent $parrent, array $data): Parrent;
    public function deleteParrent(Parrent $parrent): bool;

}
