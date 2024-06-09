<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Interfaces\ServicesInterfaces\IUserService;
use App\Models\User;
use App\Repositories\UserRepository;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService implements IUserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    final public function getProfile(): array
    {
        try {
            return (new UserResource($this->userRepository->profile()))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getAllActiveUsers(): array
    {
        try {
            $users = $this->userRepository->getAllActiveUsers();
            $usersResp = $users->toArray();
            $usersResp['data'] = UserResource::collection($users->getCollection())->resolve();
            return $usersResp;
        } catch (Exception $exception) {
            throw $exception;
        }

    }

    final public function getAllNotActiveUsers(): array
    {
        try {
            $users = $this->userRepository->getAllNotActiveUsers();
            $usersResp = $users->toArray();
            $usersResp['data'] = UserResource::collection($users->getCollection())->resolve();
            return $usersResp;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function createUser(array $userData): array
    {
        try {
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
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function updateUser(int $userId, array $data): array
    {
        try {
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
                        if (!$newRole) throw new Exception('Role not found');
                        $user->assignRole($newRole);
                    }
                }
            }
            return (new UserResource($this->userRepository->updateUser($user, $data)))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function deactivateUser(int $userId): array
    {
        try {
            $user = $this->userRepository->getUserById($userId);
            return (new UserResource($this->userRepository->deactivateUser($user)))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function reactivateUser(int $userId): array
    {
        try {
            $user = $this->userRepository->getUserById($userId);
            return (new UserResource($this->userRepository->reactivateUser($user)))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function deleteUser(int $userId): array
    {
        try {
            $user = $this->userRepository->getUserById($userId);
            return ['success' => $this->userRepository->deleteUser($user), 'user' => $user];
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
