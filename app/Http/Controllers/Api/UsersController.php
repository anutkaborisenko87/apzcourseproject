<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UsersRequests\CreateUserRequest;
use App\Http\Requests\Api\UsersRequests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\Response;
use Mockery\Exception;


class UsersController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    final public function indexProfile(): Response
    {
        try {
            $profile = $this->userService->getProfile();
            return response($profile);
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function indexActiveUsers(): Response
    {
        try {
            $activeUsers = $this->userService->getAllActiveUsers();
            return response($activeUsers);
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function indexNotActiveUsers(): Response
    {
        try {
            $activeUsers = $this->userService->getAllNotActiveUsers();
            return response($activeUsers);
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function store(CreateUserRequest $request): Response
    {
        try {
            $activeUsers = $this->userService->createUser($request->validated());
            return response($activeUsers);
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function update(int $userId, UpdateUserRequest $request): Response
    {
        try {
            $updatedUser = $this->userService->updateUser($userId, $request->validated());
            return response($updatedUser);
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function reactivateUser(int $userId): Response
    {
        try {
            $updatedUser = $this->userService->reactivateUser($userId);
            return response($updatedUser);
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function deactivateUser(int $userId): Response
    {
        try {
            $updatedUser = $this->userService->deactivateUser($userId);
            return response($updatedUser);
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function destroy(int $userId): Response
    {
        try {
            $deletedUser = $this->userService->deleteUser($userId);
            return response($deletedUser);
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

}
