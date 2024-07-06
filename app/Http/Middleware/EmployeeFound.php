<?php

namespace App\Http\Middleware;

use App\Exceptions\EmployeesControllerException;
use App\Models\Employee;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeFound
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws EmployeesControllerException
     */
    public function handle(Request $request, Closure $next)
    {
        $employee = Employee::find($request->route('employee'));
        if (!$employee) {
            throw EmployeesControllerException::getEmployeeByIdError($request->route('employee'));
        }
        return $next($request);
    }
}
