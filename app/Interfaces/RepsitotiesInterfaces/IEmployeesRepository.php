<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface IEmployeesRepository
{
    public function getAllActiveEmployees(): LengthAwarePaginator;
    public function getAllNotActiveEmployees(): LengthAwarePaginator;
    public function getAllWorkingEmployees(): LengthAwarePaginator;
    public function getEmployeeById(int $id): ?Employee;
    public function createEmployee(array $data): Employee;
    public function updateEmployee(Employee $employee, array $data): Employee;
    public function deleteEmployee(Employee $employee): bool;
    public function getActiveTeachersForGroup(): Collection;
}
