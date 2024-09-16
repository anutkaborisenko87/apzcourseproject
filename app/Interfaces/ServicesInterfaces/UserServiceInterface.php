<?php

namespace App\Interfaces\ServicesInterfaces;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface UserServiceInterface
{
    public function getProfile(): array;
    public function getAllActiveUsers(Request $request): array;
    public function getAllNotActiveUsers(Request $request): array;
    public function createUser(array $userData): array;
    public function getUserById(int $userId): array;
    public function updateUser(int $userId, array $data): array;
    public function deactivateUser(int $userId): array;
    public function reactivateUser(int $userId): array;
    public function deleteUser(int $userId): array;
}
