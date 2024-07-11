<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Interfaces\RepsitotiesInterfaces\IUserRepository;
use App\Interfaces\ServicesInterfaces\IUserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService implements IUserService
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    final public function getProfile(): array
    {
        return (new UserResource($this->userRepository->profile()))->resolve();
    }

    final public function getAllActiveUsers(Request $request): array
    {
        $users = $this->userRepository->getAllActiveUsers($request);
        return $this->formatRespData($users, $request);
    }

    final public function getAllNotActiveUsers(Request $request): array
    {
        $users = $this->userRepository->getAllNotActiveUsers($request);
        return $this->formatRespData($users, $request);
    }

    private function formatRespData(LengthAwarePaginator $usersPaginated, Request $request): array
    {
        $usersResp = $usersPaginated->toArray();
        $usersResp['data'] = UserResource::collection($usersPaginated->getCollection())->resolve();
        $requestData = $request->except(['page', 'per_page']);
        return array_merge($usersResp, $requestData);
    }

    final public function createUser(array $userData): array
    {
        $roleId = false;
        if (isset($userData['role'])) {
            $roleId = $userData['role'];
            unset($userData['role']);
        }
        if (isset($userData['email'])) {
            $userData['password'] = Hash::make($userData['email']);
        }
        if (isset($userData['birth_date'])) {
            $dateObject = new DateTime($userData['birth_date']);
            $userData['birth_date'] = $dateObject->format('Y-m-d');
            $userData['birth_year'] = $dateObject->format('Y');
        }
        $user = $this->userRepository->createUser($userData);
        if ($roleId) {
            $role = Role::findById($roleId);
            $user->assignRole($role);
        }
        return (new UserResource($user))->resolve();
    }

    final public function getUserById(int $userId): array
    {
        $user = $this->userRepository->getUserById($userId);
        if (!$user) throw new Exception("Користувача не знайдено");
        return (new UserResource($user))->resolve();
    }

    final public function updateUser(int $userId, array $data): array
    {
        $user = $this->userRepository->getUserById($userId);
        if (isset($userData['role'])) {
            $roleId = $userData['role'];
            unset($userData['role']);
            $role = $user->roles()->first();
            if (is_null($roleId) && $role) {
                $user->removeRole($role);
            }
            if ($role) {
                if ($role->id !== $roleId) {
                    $user->removeRole($role);
                    $newRole = Role::findById($userId);
                    if (!$newRole) throw new Exception('Роль не знайдено');
                    $user->assignRole($newRole);
                }
            }
        }
        return (new UserResource($this->userRepository->updateUser($user, $data)))->resolve();
    }

    final public function deactivateUser(int $userId): array
    {
        $user = $this->userRepository->getUserById($userId);
        return (new UserResource($this->userRepository->deactivateUser($user)))->resolve();
    }

    final public function reactivateUser(int $userId): array
    {
        $user = $this->userRepository->getUserById($userId);
        return (new UserResource($this->userRepository->reactivateUser($user)))->resolve();
    }

    final public function deleteUser(int $userId): array
    {
        $user = $this->userRepository->getUserById($userId);
        return ['success' => $this->userRepository->deleteUser($user), 'user' => $user];
    }
}
