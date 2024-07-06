<?php

namespace App\Services;

use App\Http\Resources\EmployeeResource;
use App\Http\Resources\TeachersForSelectResource;
use App\Interfaces\ServicesInterfaces\IEmployeesService;
use App\Repositories\EmployeesRepository;
use App\Repositories\UserRepository;
use Spatie\Permission\Models\Role;

class EmployeesService implements IEmployeesService
{
    private EmployeesRepository $teacherRepository;
    private UserRepository $userRepository;

    public function __construct(EmployeesRepository $teacherRepository, UserRepository $userRepository)
    {
        $this->teacherRepository = $teacherRepository;
        $this->userRepository = $userRepository;
    }

    final public function getActiveEmployeesList(): array
    {
        $teachers = $this->teacherRepository->getAllActiveEmployees();
        $respData = $teachers->toArray();
        $respData['data'] = EmployeeResource::collection($teachers->getCollection())->resolve();
        return $respData;
    }

    final public function getActiveTeachersList(): array
    {
        $teachers = $this->teacherRepository->getActiveTeachersForGroup();
        return TeachersForSelectResource::collection($teachers)->resolve();
    }

    final public function getNotActiveEmployeesList(): array
    {
        $teachers = $this->teacherRepository->getAllNotActiveEmployees();
        $respData = $teachers->toArray();
        $respData['data'] = EmployeeResource::collection($teachers->getCollection())->resolve();
        return $respData;
    }

    final public function getWorkingEmployeesList(): array
    {
        $teachers = $this->teacherRepository->getAllWorkingEmployees();
        $respData = $teachers->toArray();
        $respData['data'] = EmployeeResource::collection($teachers->getCollection())->resolve();
        return $respData;
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
        $createdEmployee = $this->teacherRepository->createEmployee($employeeData);
        if (!$createdEmployee) {
            $this->userRepository->deleteUser($createdUser);
        }
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
