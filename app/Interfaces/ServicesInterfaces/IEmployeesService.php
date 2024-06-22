<?php

namespace App\Interfaces\ServicesInterfaces;

interface IEmployeesService
{
    public function getActiveEmployeesList(): array;
    public function getActiveTeachersList(): array;
    public function getNotActiveEmployeesList(): array;
    public function getWorkingEmployeesList(): array;
    public function createEmployee(array $data): array;
    public function showEmployeeInfo(int $id): array;
    public function fireEmployee(int $id, array $data): array;
    public function updateEmployee(int $id, array $data): array;
    public function deactivateEmployee(int $id): array;
    public function reactivateEmployee(int $id): array;
    public function deleteEmployee(int $id): array;

}
