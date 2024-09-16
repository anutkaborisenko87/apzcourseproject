<?php

namespace App\Interfaces\ServicesInterfaces;

use Illuminate\Http\Request;

interface EmployeesServiceInterface
{
    public function getActiveEmployeesList(Request $request): array;
    public function getActiveTeachersList(): array;
    public function getNotActiveEmployeesList(Request $request): array;
    public function getWorkingEmployeesList(Request $request): array;
    public function createEmployee(array $data): array;
    public function showEmployeeInfo(int $id): array;
    public function fireEmployee(int $id, array $data): array;
    public function updateEmployee(int $id, array $data): array;
    public function deactivateEmployee(int $id): array;
    public function reactivateEmployee(int $id): array;
    public function deleteEmployee(int $id): array;

}
