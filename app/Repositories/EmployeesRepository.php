<?php

namespace App\Repositories;

use App\Exceptions\EmployeesControllerException;
use App\Interfaces\RepsitotiesInterfaces\IEmployeesRepository;
use App\Models\Employee;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeesRepository implements IEmployeesRepository
{

    final public function getAllActiveEmployees(): LengthAwarePaginator
    {
        try {
            return Employee::whereHas('user', function ($query) {
                $query->where('active', true);
            })->with('user')->paginate(10);
        } catch (Exception $exception) {
            throw EmployeesControllerException::getEmployeesListError($exception->getCode());
        }
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
            throw EmployeesControllerException::getEmployeesListError($exception->getCode());
        }

    }

    final public function getAllNotActiveEmployees(): LengthAwarePaginator
    {
        try {
            return Employee::whereHas('user', function ($query) {
                $query->where('active', false);
            })->with('user')->paginate(10);
        } catch (Exception $exception) {
            throw EmployeesControllerException::getEmployeesListError($exception->getCode());
        }

    }

    final public function getAllWorkingEmployees(): LengthAwarePaginator
    {
        try {
            return Employee::whereHas('user', function ($query) {
                $query->where('active', true);
            })
                ->whereNotNull('employment_date')
                ->whereNull('date_dismissal')
                ->with('user')->paginate(10);
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

    final public function createEmployee(array $data): Employee
    {
        try {
            $employee = Employee::create($data);
            if (!$employee) throw EmployeesControllerException::createEmployeeError(Response::HTTP_BAD_REQUEST);
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
