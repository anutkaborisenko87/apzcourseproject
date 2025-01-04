<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Interfaces\RepsitotiesInterfaces\UserRepositoryInterface;
use App\Interfaces\ServicesInterfaces\UserServiceInterface;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
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
        return $this->formatRespData($users, $request, true);
    }

    final public function getAllNotActiveUsers(Request $request): array
    {
        $users = $this->userRepository->getAllNotActiveUsers($request);
        return $this->formatRespData($users, $request, false);
    }

    private function formatRespData(LengthAwarePaginator $usersPaginated, Request $request, bool $active): array
    {
        $usersResp = $usersPaginated->toArray();
        $usersResp['data'] = UserResource::collection($usersPaginated->getCollection())->resolve();
        $requestData = $request->except(['page', 'per_page']);
        $requestedYearFilters = isset($request->input('date_filter_users_by')['birth_date']) ? $request->input('date_filter_users_by')['birth_date'] : [];
        $yearsFilterOption = [];

        $yearsFilterOption['from'] = [
            'value' => $requestedYearFilters['from'] ?? null,
            'label' => "Від дати",
            'min' => $this->userRepository->getMinBirthDateUsers($active),
            'max' => $this->userRepository->getMaxBirthDateUsers($active)
        ];
        $yearsFilterOption['to'] = [
            'value' => $requestedYearFilters['to'] ?? null,
            'label' => "До дати",
            'min' => $this->userRepository->getMinBirthDateUsers($active),
            'max' => $this->userRepository->getMaxBirthDateUsers($active)
        ];

        $requestData['filters'][] = [
            'id' => 'category',
            'name' => 'Категорія',
            'options' => [
                [
                    'value' => 'parents',
                    'label' => 'батьки',
                    'checked' => $request->has('filter_users_by')
                        && in_array('category', array_keys($request->input('filter_users_by')))
                        && in_array('parents', $request->input('filter_users_by')['category'])
                ],
                [
                    'value' => 'children',
                    'label' => 'діти',
                    'checked' => $request->has('filter_users_by')
                        && in_array('category', array_keys($request->input('filter_users_by')))
                        && in_array('children', $request->input('filter_users_by')['category'])
                ],
                [
                    'value' => 'employees',
                    'label' => 'співробітники',
                    'checked' => $request->has('filter_users_by')
                        && in_array('category', array_keys($request->input('filter_users_by')))
                        && in_array('employees', $request->input('filter_users_by')['category'])
                ],
                [
                    'value' => 'users',
                    'label' => 'адмін. персонал',
                    'checked' => $request->has('filter_users_by')
                        && in_array('category', array_keys($request->input('filter_users_by')))
                        && in_array('users', $request->input('filter_users_by')['category'])
                ],
            ]
        ];
        if (!empty($yearsFilterOption)) {
            $requestData['dateFilters'][] = array_merge([
                'id' => 'birth_date',
                'name' => 'Дата народження',
            ], $yearsFilterOption);
        }


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

    /**
     * Updates the user information based on the provided data.
     *
     * This method retrieves a user by their ID and updates their details
     * including roles, birth date, and email. Handles role assignment and
     * removal based on the given role ID. Automatically formats the birth
     * date and generates a hashed password based on the email if specified.
     *
     * @param int $userId The ID of the user to be updated.
     * @param array $data An associative array containing the updated user data.
     *
     * @return array The updated user data resolved through the UserResource.
     *
     * @throws Exception If a specified role ID does not correspond to a valid role.
     */
    final public function updateUser(int $userId, array $data): array
    {
        $user = $this->userRepository->getUserById($userId);
        $userData = $data;
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
        if (isset($userData['birth_date'])) {
            $dateObject = new DateTime($userData['birth_date']);
            $userData['birth_date'] = $dateObject->format('Y-m-d');
            $userData['birth_year'] = $dateObject->format('Y');
        }
        if (isset($userData['email'])) {
            $userData['password'] = Hash::make($userData['email']);
        }
        return (new UserResource($this->userRepository->updateUser($user, $userData)))->resolve();
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
