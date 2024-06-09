<?php

namespace App\Interfaces\ServicesInterfaces;

use App\Models\User;

interface IUserService
{
    public function getProfile(): array;
    public function getAllActiveUsers(): array;
    public function getAllNotActiveUsers(): array;
    public function createUser(array $userData): array;
    public function getUserById(int $userId): array;
    public function updateUser(int $userId, array $data): array;
    public function deactivateUser(int $userId): array;
    public function reactivateUser(int $userId): array;
    public function deleteUser(int $userId): array;
}
