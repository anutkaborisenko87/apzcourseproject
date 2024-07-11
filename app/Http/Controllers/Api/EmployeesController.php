<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EmployeesRequests\CreateEmployeeRequest;
use App\Http\Requests\Api\EmployeesRequests\FireEmployeeRequest;
use App\Http\Requests\Api\EmployeesRequests\UpdateEmployeeRequest;
use App\Interfaces\ServicesInterfaces\IEmployeesService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeesController extends Controller
{
    private IEmployeesService $employeeService;

    public function __construct(IEmployeesService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    final public function indexActiveEmployees(Request $request): Response
    {
        return response($this->employeeService->getActiveEmployeesList($request));
    }

    final public function indexActiveTeachers(): Response
    {
        return response($this->employeeService->getActiveTeachersList());
    }

    final public function indexNotActiveEmployees(Request $request): Response
    {
        return response($this->employeeService->getNotActiveEmployeesList($request));

    }

    final public function indexWorkingEmployees(Request $request): Response
    {
        return response($this->employeeService->getWorkingEmployeesList($request));
    }

    final public function storeEmployee(CreateEmployeeRequest $request): Response
    {
        return response($this->employeeService->createEmployee($request->validated()));
    }

    final public function update(UpdateEmployeeRequest $request, int $emoployeeId): Response
    {
        return response($this->employeeService->updateEmployee($emoployeeId, $request->validated()));
    }

    final public function fireEmployee(FireEmployeeRequest $request, int $emoployeeId): Response
    {
        return response($this->employeeService->fireEmployee($emoployeeId, $request->validated()));
    }

    final public function destroy(int $emoployeeId): Response
    {
        return response($this->employeeService->deleteEmployee($emoployeeId));
    }

    final public function reactivateEmployee(int $emoployeeId): Response
    {
        return response($this->employeeService->reactivateEmployee($emoployeeId));
    }

    final public function deactivateEmployee(int $emoployeeId): Response
    {
        return response($this->employeeService->deactivateEmployee($emoployeeId));
    }

    final public function showEmployee(int $emoployeeId): Response
    {
        return response($this->employeeService->showEmployeeInfo($emoployeeId));
    }
}
