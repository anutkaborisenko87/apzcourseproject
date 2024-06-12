<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EmployeesRequests\CreateEmployeeRequest;
use App\Http\Requests\Api\EmployeesRequests\UpdateEmployeeRequest;
use App\Services\EmployeesService;
use Exception;
use Illuminate\Http\Response;

class EmployeesController extends Controller
{
    private $employeeService;

    public function __construct(EmployeesService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    final public function indexActiveEmployees(): Response
    {
        try {
            return response($this->employeeService->getActiveEmployeesList());
        } catch (Exception $exception) {
            return response(['error' => $exception], 400);
        }

    }

    final public function indexNotActiveEmployees(): Response
    {
        try {
            return response($this->employeeService->getNotActiveEmployeesList());
        } catch (Exception $exception) {
            return response(['error' => $exception], 400);
        }

    }

    final public function indexWorkingEmployees(): Response
    {
        try {
            return response($this->employeeService->getWorkingEmployeesList());
        } catch (Exception $exception) {
            return response(['error' => $exception], 400);
        }

    }

    final public function storeEmployee(CreateEmployeeRequest $request): Response
    {
        try {
            return response($this->employeeService->createEmployee($request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }

    }

    final public function update(UpdateEmployeeRequest $request, int $emoployeeId): Response
    {
        try {
            return response($this->employeeService->updateEmployee($emoployeeId, $request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }

    }

    final public function destroy(int $emoployeeId): Response
    {
        try {
            return response($this->employeeService->deleteEmployee($emoployeeId));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }

    }

    final public function reactivateEmployee(int $emoployeeId): Response
    {
        try {
            return response($this->employeeService->reactivateEmployee($emoployeeId));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }

    }

    final public function deactivateEmployee(int $emoployeeId): Response
    {
        try {
            return response($this->employeeService->deactivateEmployee($emoployeeId));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }

    }

    final public function showEmployee(int $emoployeeId): Response
    {
        try {
            return response($this->employeeService->showEmployeeInfo($emoployeeId));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }

    }
}
