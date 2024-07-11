<?php

namespace App\Interfaces\ServicesInterfaces;

use Illuminate\Http\Request;

interface IChildrenService
{
    public function childrenForSelectList(): array;
    public function childrenForUpdateSelectList(int $parrenId): array;
    public function childrenForGroupSelectList(): array;
    public function allChildrenList(Request $request): array;
    public function allChildrenForEnrolmentList(Request $request): array;
    public function allChildrenInTrainingList(Request $request): array;
    public function allGraduatedChildrenList(Request $request): array;
    public function getChildInfo(int $childId): array;
    public function addChildInfo(array $data): array;
    public function updateChildInfo(int $childId, array $data): array;
    public function deleteChildInfo(int $childId): array;
}
