<?php

namespace App\Repositories;

use App\Exceptions\EmployeesControllerException;
use App\Interfaces\RepsitotiesInterfaces\IEmployeesRepository;
use App\Models\Employee;
use App\Models\User;
use App\QueryFilters\EmployeeSearchBy;
use App\QueryFilters\EmployeeSortBy;
use App\QueryFilters\UserSearchBy;
use App\QueryFilters\UserSortBy;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class EmployeesRepository implements IEmployeesRepository
{

    final public function getAllActiveEmployees(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatData(User::where('active', true), $request);
        } catch (Exception $exception) {
            throw EmployeesControllerException::getEmployeesListError($exception->getCode() ?? Response::HTTP_BAD_REQUEST);
        }
    }

    private function formatData($usersQuery, Request $request, ?bool $working = false): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);
        $users = app(Pipeline::class)
            ->send($usersQuery)
            ->through([
                UserSearchBy::class,
                UserSortBy::class,
            ])->thenReturn();
        $usersIds = $users->pluck('id')->toArray();
        $employees = Employee::whereHas('user', function ($query) use ($usersIds) {
            $query->whereIn('id', $usersIds);
        })->with('user');
        $employees = app(Pipeline::class)
            ->send($employees)
            ->through([
                EmployeeSortBy::class,
                EmployeeSearchBy::class
            ])
            ->thenReturn();
        if ($working) {
            $employees = $employees ->whereNotNull('employment_date')->whereNull('date_dismissal');
        }

        if ($perPage !== 'all') {
            $employees =  $employees->paginate((int) $perPage);
        } else {
            $employees =  $employees->paginate($employees->count());
        }
        return $employees;
    }

    final public function getActiveTeachersForGroup(): Collection
    {
        try {
            $today = date('Y-m-d');
            return Employee::whereNotNull('employment_date')
                ->whereNull('date_dismissal')
                ->whereHas('user', function ($query) {
                    $query->where('active', true);
                })->whereHas('position', function ($query) {
                    $query->where('position_title', 'teacher');
                })->whereDoesntHave('groups')
                ->orWhereHas('groups', function ($query) use ($today) {
                    $query->where('employees_groups.date_finish', '<', $today);
                })->with('user')->get();
        } catch (Exception $exception) {
            throw EmployeesControllerException::getEmployeesListError($exception->getCode() ?? Response::HTTP_BAD_REQUEST);
        }

    }

    final public function getAllNotActiveEmployees(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatData(User::where('active', false), $request);
        } catch (Exception $exception) {
            throw EmployeesControllerException::getEmployeesListError($exception->getCode() ?? Response::HTTP_BAD_REQUEST);
        }

    }

    final public function getAllWorkingEmployees(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatData(User::where('active', true), $request, true);
        } catch (Exception $exception) {
            throw EmployeesControllerException::getEmployeesListError($exception->getCode());
        }
    }

    final public function getEmployeeById(int $id): Employee
    {
        try {
            $employee = Employee::find($id);
            if (!$employee) throw EmployeesControllerException::getEmployeeByIdError($id);
            return $employee;
        } catch (Exception $exception) {
            throw EmployeesControllerException::getEmployeeByIdError($id);
        }
    }

    final public function createEmployee(array $data, User $user): Employee
    {
        try {
            $employee = Employee::create($data);
            if (!$employee) {
                $user->delete();
                throw EmployeesControllerException::createEmployeeError(Response::HTTP_BAD_REQUEST);
            }
            return $employee;
        } catch (Exception $exception) {
            throw EmployeesControllerException::createEmployeeError($exception->getCode());
        }
    }

    final public function updateEmployee(Employee $employee, array $data): Employee
    {
        try {
            if (!$employee->update($data)) throw EmployeesControllerException::updateEmployeeError(Response::HTTP_BAD_REQUEST);
            return $employee;
        } catch (Exception $exception) {
            throw EmployeesControllerException::updateEmployeeError($exception->getCode());
        }
    }

    final public function deleteEmployee(Employee $employee): bool
    {
        try {
            if (!$employee->delete()) throw EmployeesControllerException::deleteEmployeeError(Response::HTTP_BAD_REQUEST);
            return true;
        } catch (Exception $exception) {
            throw EmployeesControllerException::deleteEmployeeError($exception->getCode());
        }
    }
}
