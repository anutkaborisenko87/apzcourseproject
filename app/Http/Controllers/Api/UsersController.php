<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UsersRequests\CreateUserRequest;
use App\Http\Requests\Api\UsersRequests\UpdateUserRequest;
use App\Interfaces\ServicesInterfaces\IUserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class UsersController extends Controller
{
    private IUserService $userService;

    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    final public function indexProfile(): Response
    {
        $profile = $this->userService->getProfile();
        return response($profile);
    }

    final public function indexActiveUsers(Request $request): Response
    {
        $activeUsers = $this->userService->getAllActiveUsers($request);
        return response($activeUsers);
    }

    final public function indexNotActiveUsers(Request $request): Response
    {
        $activeUsers = $this->userService->getAllNotActiveUsers($request);
        return response($activeUsers);
    }

    final public function showUser(int $userId): Response
    {
        $user = $this->userService->getUserById($userId);
        return response($user);
    }

    final public function store(CreateUserRequest $request): Response
    {
        $activeUsers = $this->userService->createUser($request->validated());
        return response($activeUsers);
    }

    final public function update(int $userId, UpdateUserRequest $request): Response
    {
        $updatedUser = $this->userService->updateUser($userId, $request->validated());
        return response($updatedUser);
    }

    final public function reactivateUser(int $userId): Response
    {
        $updatedUser = $this->userService->reactivateUser($userId);
        return response($updatedUser);
    }

    final public function deactivateUser(int $userId): Response
    {
        $updatedUser = $this->userService->deactivateUser($userId);
        return response($updatedUser);
    }

    final public function destroy(int $userId): Response
    {
        $deletedUser = $this->userService->deleteUser($userId);
        return response($deletedUser);
    }

}
