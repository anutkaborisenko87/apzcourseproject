<?php

namespace App\Repositories;

use App\Exceptions\EmployeesControllerException;
use App\Interfaces\RepsitotiesInterfaces\EmployeesRepositoryInterface;
use App\Models\Employee;
use App\Models\User;
use App\QueryFilters\DateFilterEmployeesBy;
use App\QueryFilters\EmployeeSearchBy;
use App\QueryFilters\EmployeeSortBy;
use App\QueryFilters\FilterEmployeesBy;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EmployeesRepository implements EmployeesRepositoryInterface
{

    /**
     * Retrieves all active employees with pagination.
     *
     * @param Request $request The incoming request instance.
     * @return LengthAwarePaginator Paginated list of active employees.
     * @throws EmployeesControllerException If an error occurs while retrieving employees.
     */
    final public function getAllActiveEmployees(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatData(true, $request);
        } catch (Exception $exception) {
            throw EmployeesControllerException::getEmployeesListError(ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Formats and retrieves a paginated list of employees based on the specified criteria.
     *
     * The method performs the following steps:
     * - Filters employees by their user activity status (`useractive`).
     * - Applies additional query pipelines using `EmployeeSortBy`, `EmployeeSearchBy`, and `FilterEmployeesBy` classes.
     * - Optionally filters for currently working employees who have an `employment_date` but no `date_dismissal`, based on the `working` parameter.
     * - Paginates the results using the `per_page` value from the request. If the value is 'all', retrieves all records.
     *
     * @param bool $useractive Indicates whether to filter by active or inactive users.
     * @param Request $request The HTTP request containing additional parameters (e.g., `per_page`).
     * @param bool|null $working Optional. Defaults to false. Filters for currently working employees if true.
     *
     * @return LengthAwarePaginator A paginated collection of employees based on the applied filters and criteria.
     */
    private function formatData(bool $useractive, Request $request, ?bool $working = false): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);

        $employees = Employee::whereHas('user', function ($query) use ($useractive) {
            $query->where('active', $useractive);
        })->with('user');
        $employees = app(Pipeline::class)
            ->send($employees)
            ->through([
                DateFilterEmployeesBy::class,
                EmployeeSortBy::class,
                EmployeeSearchBy::class,
                FilterEmployeesBy::class
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

    /**
     * Retrieves a collection of active teachers for a specific group.
     *
     * Active teachers are determined based on the following conditions:
     * - The teacher has an employment date (`employment_date`) and no dismissal date (`date_dismissal`).
     * - The associated user is active.
     * - The person holds a position with the title 'teacher'.
     * - The teacher either does not belong to any groups or belongs to groups where the `date_finish` is earlier than the current date.
     *
     * Includes the associated user relationships for eager loading.
     *
     * @return Collection A collection of active teachers matching the defined criteria.
     * @throws EmployeesControllerException If an error occurs while retrieving the list of employees.
     *
     */
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
            throw EmployeesControllerException::getEmployeesListError($exception->getCode() ?? ResponseAlias::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     *
     * @throws EmployeesControllerException
     */
    final public function getAllNotActiveEmployees(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatData(false, $request);
        } catch (Exception $exception) {
            throw EmployeesControllerException::getEmployeesListError($exception->getCode() ?? ResponseAlias::HTTP_BAD_REQUEST);
        }

    }

    /**
     * Retrieves a paginated list of all currently working employees.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     * @throws EmployeesControllerException
     */
    final public function getAllWorkingEmployees(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatData(true, $request, true);
        } catch (Exception $exception) {
            throw EmployeesControllerException::getEmployeesListError($exception->getCode());
        }
    }

    /**
     * Retrieves an employee record by the specified ID.
     *
     * @param int $id The unique identifier of the employee.
     * @return Employee The employee record associated with the given ID.
     *
     * @throws EmployeesControllerException If the employee is not found or an error occurs during retrieval.
     */
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

    /**
     * Create a new employee.
     *
     * @param array $data The data required to create the employee.
     * @param User $user The user associated with the employee.
     *
     * @return Employee The newly created employee instance.
     *
     * @throws EmployeesControllerException
     */
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

    /**
     * @param Employee $employee The employee instance to be updated.
     * @param array $data The data for updating the employee.
     *
     * @return Employee The updated employee instance.
     *
     * @throws EmployeesControllerException
     */
    final public function updateEmployee(Employee $employee, array $data): Employee
    {
        try {
            if (!$employee->update($data)) throw EmployeesControllerException::updateEmployeeError(Response::HTTP_BAD_REQUEST);
            return $employee;
        } catch (Exception $exception) {
            throw EmployeesControllerException::updateEmployeeError($exception->getCode());
        }
    }

    /**
     * Deletes the given employee from the system.
     *
     * @param Employee $employee The employee instance to be deleted.
     *
     * @return bool Returns true if the employee was successfully deleted.
     * @throws EmployeesControllerException
     */
    final public function deleteEmployee(Employee $employee): bool
    {
        try {
            if (!$employee->delete()) throw EmployeesControllerException::deleteEmployeeError(Response::HTTP_BAD_REQUEST);
            return true;
        } catch (Exception $exception) {
            throw EmployeesControllerException::deleteEmployeeError($exception->getCode());
        }
    }

    public function getMinEmploymentDate(bool $active): string
    {
        return Employee::whereHas('user', function ($query) use ($active) {
            $query->where('active', $active);
        })->min('employment_date') ?? '';
    }
}
