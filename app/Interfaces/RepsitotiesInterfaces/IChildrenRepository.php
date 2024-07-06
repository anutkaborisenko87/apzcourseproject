<?php

namespace App\Interfaces\RepsitotiesInterfaces;


use App\Models\Children;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface IChildrenRepository
{
    public function getChildrenForSelect(): Collection;
    public function getChildrenForUpdateSelect(int $parrentId): Collection;
    public function getChildrenForGroupSelect(): Collection;
    public function getAllChildrenList(): LengthAwarePaginator;
    public function getAllChildrenForEnrollment(): LengthAwarePaginator;
    public function getAllChildrenInTraining(): LengthAwarePaginator;
    public function getAllGraduatedChildren(): LengthAwarePaginator;
    public function getChildById(int $childId): Children;
    public function createChildInfo(array $data): Children;
    public function updateChildInfo(Children $child, array $data): Children;
    public function deleteChildInfo(Children $child): bool;

}
