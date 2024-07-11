<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface IEmployeesRepository
{
    public function getAllActiveEmployees(Request $request): LengthAwarePaginator;
    public function getAllNotActiveEmployees(Request $request): LengthAwarePaginator;
    public function getAllWorkingEmployees(Request $request): LengthAwarePaginator;
    public function getEmployeeById(int $id): Employee;
    public function createEmployee(array $data, User $user): Employee;
    public function updateEmployee(Employee $employee, array $data): Employee;
    public function deleteEmployee(Employee $employee): bool;
    public function getActiveTeachersForGroup(): Collection;
}
