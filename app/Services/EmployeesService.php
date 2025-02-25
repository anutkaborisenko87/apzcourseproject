<?php

namespace App\Services;

use App\Http\Resources\EmployeeResource;
use App\Http\Resources\TeachersForSelectResource;
use App\Interfaces\RepsitotiesInterfaces\EmployeesRepositoryInterface;
use App\Interfaces\RepsitotiesInterfaces\UserRepositoryInterface;
use App\Interfaces\ServicesInterfaces\EmployeesServiceInterface;
use App\Models\Employee;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;
use function PHPUnit\Framework\countOf;

class EmployeesService implements EmployeesServiceInterface
{
    private EmployeesRepositoryInterface $teacherRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(EmployeesRepositoryInterface $teacherRepository, UserRepositoryInterface $userRepository)
    {
        $this->teacherRepository = $teacherRepository;
        $this->userRepository = $userRepository;
    }

    final public function getActiveEmployeesList(Request $request): array
    {
        $employees = $this->teacherRepository->getAllActiveEmployees($request);
        return $this->formatRespData($employees, $request, true);
    }

    private function formatRespData(LengthAwarePaginator $employeesPaginated, Request $request, bool $active): array
    {
        $employeesResp = $employeesPaginated->toArray();
        $employeesResp['data'] = EmployeeResource::collection($employeesPaginated->getCollection())->resolve();
        $today = Carbon::today();
        $groups = Group::whereHas('teachers', function ($query) use ($today) {
            $query->where('date_start', '<', $today)
                ->where('date_finish', '>', $today);
        })->pluck('title', 'id')->toArray();
        $groupsOptions = [];
        if (count($groups) > 0) {
            array_walk($groups, function ($group, $key) use (&$groupsOptions, $request) {
                $groupsOptions[] = [
                    'value' => $key,
                    'label' => $group,
                    'checked' => $request->has('filter_employees_by')
                        && in_array('group', array_keys($request->input('filter_employees_by')))
                        && in_array($key, array_values($request->input('filter_employees_by')['group']))
                ];

            });
        }

        $requestData = $request->except(['page', 'per_page']);
        if (count($groupsOptions) > 0) {
            $requestData['filters'][] = [
                'id' => 'group',
                'name' => 'Група викладання',
                'options' => $groupsOptions
            ];
        }
        $citiesList = User::where('active', $active)->whereHas('employee')->citiesList()->toArray();
        $citiesList = array_map(function ($city) use ($request) {
            $cityString = $city ?? 'null';
            return [
                'value' => $cityString,
                'label' => $city ?? 'Невідомо',
                'checked' => $request->has('filter_employees_by')
                    && in_array('city', array_keys($request->input('filter_employees_by')))
                    && in_array($cityString, $request->input('filter_employees_by')['city'])
            ];
        }, $citiesList);
        $requestData['filters'][] = [
            'id' => 'city',
            'name' => 'Місто',
            'options' => $citiesList
        ];
        $employmentDateOptions = [];
        $requestedYearFilters = isset($request->input('date_filter_employees_by')['employment_date']) ? $request->input('date_filter_employees_by')['employment_date'] : [];
        $employmentDateOptions['from'] = [
            'value' => $requestedYearFilters['from'] ?? null,
            'label' => "Від дати",
            'min' => $this->teacherRepository->getMinEmploymentDate($active),
            'max' => Carbon::today()->format("Y-m-d")
        ];
        $employmentDateOptions['to'] = [
            'value' => $requestedYearFilters['to'] ?? null,
            'label' => "До дати",
            'min' => $this->teacherRepository->getMinEmploymentDate($active),
            'max' => Carbon::today()->format("Y-m-d")
        ];
        if (!empty($employmentDateOptions)) {
            $requestData['dateFilters'][] = array_merge([
                'id' => 'employment_date',
                'name' => 'Дата прийому на роботу',
            ], $employmentDateOptions);
        }

        return array_merge($employeesResp, $requestData);
    }

    final public function getActiveTeachersList(): array
    {
        $teachers = $this->teacherRepository->getActiveTeachersForGroup();
        return TeachersForSelectResource::collection($teachers)->resolve();
    }

    final public function getNotActiveEmployeesList(Request $request): array
    {
        $employees = $this->teacherRepository->getAllNotActiveEmployees($request);
        return $this->formatRespData($employees, $request, false);
    }

    final public function getWorkingEmployeesList(Request $request): array
    {
        $employees = $this->teacherRepository->getAllWorkingEmployees($request);
        return $this->formatRespData($employees, $request, true);
    }

    final public function createEmployee(array $data): array
    {
        $userData = $data['user'];
        $createdUser = $this->userRepository->createUser($data['user']);
        if (isset($userData['email'])) {
            $role = Role::where('name', 'teacher')->first();
            $createdUser->assignRole($role);
        }
        $employeeData = $data['employee'];
        $employeeData['user_id'] = $createdUser->id;
        $createdEmployee = $this->teacherRepository->createEmployee($employeeData, $createdUser);
        return (new EmployeeResource($createdEmployee))->resolve();
    }

    final public function showEmployeeInfo(int $id): array
    {
        $teacher = $this->teacherRepository->getEmployeeById($id);
        return (new EmployeeResource($teacher))->resolve();
    }

    final public function updateEmployee(int $id, array $data): array
    {
        $teacher = $this->teacherRepository->getEmployeeById($id);
        if (isset($data['user'])) {
            $this->userRepository->updateUser($teacher->user, $data['user']);
        }
        $updatedEmployee = $this->teacherRepository->updateEmployee($teacher, $data['employee']);
        return (new EmployeeResource($updatedEmployee))->resolve();
    }

    final public function deleteEmployee(int $id): array
    {
        $teacher = $this->teacherRepository->getEmployeeById($id);
        $teacherResp = (new EmployeeResource($teacher))->resolve();
        $this->userRepository->deleteUser($teacher->user);
        return ['success' => true, 'teacher' => $teacherResp];
    }

    final public function deactivateEmployee(int $id): array
    {
        $teacher = $this->teacherRepository->getEmployeeById($id);
        $this->userRepository->deactivateUser($teacher->user);
        return (new EmployeeResource($teacher))->resolve();
    }

    final public function reactivateEmployee(int $id): array
    {
        $teacher = $this->teacherRepository->getEmployeeById($id);
        $this->userRepository->reactivateUser($teacher->user);
        return (new EmployeeResource($teacher))->resolve();
    }

    final public function fireEmployee(int $id, array $data): array
    {
        $teacher = $this->teacherRepository->getEmployeeById($id);
        $this->userRepository->updateUser($teacher->user, ['active' => false]);
        if (!isset($data['date_dismissal'])) {
            $data['date_dismissal'] = date("Y-m-d");
        }
        $updatedEmployee = $this->teacherRepository->updateEmployee($teacher, $data);
        return (new EmployeeResource($updatedEmployee))->resolve();
    }
}
