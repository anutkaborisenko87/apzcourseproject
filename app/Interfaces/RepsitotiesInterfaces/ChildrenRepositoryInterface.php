<?php

namespace App\Interfaces\RepsitotiesInterfaces;


use App\Models\Children;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ChildrenRepositoryInterface
{
    public function getChildrenForSelect(): Collection;
    public function getChildrenForUpdateSelect(int $parrentId): Collection;
    public function getChildrenForGroupSelect(): Collection;
    public function getAllChildrenList(Request $request): LengthAwarePaginator;
    public function getAllChildrenForEnrollment(Request $request): LengthAwarePaginator;
    public function getAllChildrenInTraining(Request $request): LengthAwarePaginator;
    public function getAllGraduatedChildren(Request $request): LengthAwarePaginator;
    public function getChildById(int $childId): Children;
    public function createChildInfo(array $data): Children;
    public function updateChildInfo(Children $child, array $data): Children;
    public function deleteChildInfo(Children $child): bool;

}
