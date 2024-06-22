<?php

namespace App\Services;

use App\Http\Resources\EmployeeResource;
use App\Http\Resources\TeachersForSelectResource;
use App\Interfaces\ServicesInterfaces\IEmployeesService;
use App\Repositories\EmployeesRepository;
use App\Repositories\UserRepository;
use Exception;
use Spatie\Permission\Models\Role;

class EmployeesService implements IEmployeesService
{
    private $teacherRepository;
    private $userRepository;

    public function __construct(EmployeesRepository $teacherRepository, UserRepository $userRepository)
    {
        $this->teacherRepository = $teacherRepository;
        $this->userRepository = $userRepository;
    }
    final public function getActiveEmployeesList(): array
    {
        try {
            $teachers = $this->teacherRepository->getAllActiveEmployees();
            $respData = $teachers->toArray();
            $respData['data'] = EmployeeResource::collection($teachers->getCollection())->resolve();
            return $respData;
        } catch (Exception $exception) {
            throw $exception;
        }

    }
    final public function getActiveTeachersList(): array
    {
        try {
            $teachers = $this->teacherRepository->getActiveTeachersForGroup();
            return TeachersForSelectResource::collection($teachers)->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }

    }
    final public function getNotActiveEmployeesList(): array
    {
        try {
            $teachers = $this->teacherRepository->getAllNotActiveEmployees();
            $respData = $teachers->toArray();
            $respData['data'] = EmployeeResource::collection($teachers->getCollection())->resolve();
            return $respData;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getWorkingEmployeesList(): array
    {
        try {
            $teachers = $this->teacherRepository->getAllWorkingEmployees();
            $respData = $teachers->toArray();
            $respData['data'] = EmployeeResource::collection($teachers->getCollection())->resolve();
            return $respData;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function createEmployee(array $data): array
    {
        try {
            $userData = $data['user'];
            $createdUser = $this->userRepository->createUser($data['user']);
            if (!$createdUser) throw new Exception("Помилка створення користувача");
            if (isset($userData['email'])) {
                $role = Role::where('name', 'teacher')->first();
                $createdUser->assignRole($role);
            }

            $employeeData = $data['employee'];
            $employeeData['user_id'] = $createdUser->id;
            $createdEmployee = $this->teacherRepository->createEmployee($employeeData);
            if (!$createdEmployee) {
                $this->userRepository->deleteUser($createdUser);
                throw new Exception("Помилка створення вчителя");
            }
            return (new EmployeeResource($createdEmployee))->resolve();

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function showEmployeeInfo(int $id): array
    {
        try {
            $teacher = $this->teacherRepository->getEmployeeById($id);
            if (!$teacher) throw new Exception("Співробітник не знайдений");
            return (new EmployeeResource($teacher))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function updateEmployee(int $id, array $data): array
    {
        try {
            $teacher = $this->teacherRepository->getEmployeeById($id);
            if (!$teacher) throw new Exception("Співробітник не знайдений");
            if (isset($data['user'])) {
                $updatedUser = $this->userRepository->updateUser($teacher->user, $data['user']);
                if (!$updatedUser) throw new Exception("Помилка оновлення користувача");
            }
            $updatedEmployee = $this->teacherRepository->updateEmployee($teacher, $data['employee']);
            if (!$updatedEmployee) throw new Exception("Помилка оновлення вчителя");
            return (new EmployeeResource($updatedEmployee))->resolve();

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function deleteEmployee(int $id): array
    {
        try {
            $teacher = $this->teacherRepository->getEmployeeById($id);
            if (!$teacher) throw new Exception("Співробітник не знайдений");
            $teacherResp = (new EmployeeResource($teacher))->resolve();
            $deletedUser = $this->userRepository->deleteUser($teacher->user);
            if (!$deletedUser) throw new Exception("Помилка видалення вчителя");
            return ['success' => true, 'teacher' => $teacherResp];
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function deactivateEmployee(int $id): array
    {
        try {
            $teacher = $this->teacherRepository->getEmployeeById($id);
            if (!$teacher) throw new Exception("Співробітник не знайдений");
            $userDeactivated = $this->userRepository->deactivateUser($teacher->user);
            if (!$userDeactivated) throw new Exception("Помилка деактивації вчителя");
            return (new EmployeeResource($teacher))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function reactivateEmployee(int $id): array
    {
        try {
            $teacher = $this->teacherRepository->getEmployeeById($id);
            if (!$teacher) throw new Exception("Співробітник не знайдений");
            $userDeactivated = $this->userRepository->reactivateUser($teacher->user);
            if (!$userDeactivated) throw new Exception("Помилка реактивації вчителя");
            return (new EmployeeResource($teacher))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function fireEmployee(int $id, array $data): array
    {
        try {
            $teacher = $this->teacherRepository->getEmployeeById($id);
            if (!$teacher) throw new Exception("Співробітник не знайдений");
            $updatedUser = $this->userRepository->updateUser($teacher->user, ['active' => false]);
            if (!$updatedUser) throw new Exception("Помилка деактивації користувача");
            if (!isset($data['date_dismissal'])) {
                $data['date_dismissal'] = date("Y-m-d");
            }
            $updatedEmployee = $this->teacherRepository->updateEmployee($teacher, $data);
            if (!$updatedEmployee) throw new Exception("Помилка звільнення вчителя");
            return (new EmployeeResource($updatedEmployee))->resolve();

        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
