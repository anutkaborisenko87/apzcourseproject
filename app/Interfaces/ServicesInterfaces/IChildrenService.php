<?php

namespace App\Interfaces\ServicesInterfaces;

interface IChildrenService
{
    public function childrenForSelectList(): array;
    public function childrenForUpdateSelectList(int $parrenId): array;
    public function childrenForGroupSelectList(): array;
    public function allChildrenList(): array;
    public function allChildrenForEnrolmentList(): array;
    public function allChildrenInTrainingList(): array;
    public function allGraduatedChildrenList(): array;
    public function getChildInfo(int $childId): array;
    public function addChildInfo(array $data): array;
    public function updateChildInfo(int $childId, array $data): array;
    public function deleteChildInfo(int $childId): array;
}
