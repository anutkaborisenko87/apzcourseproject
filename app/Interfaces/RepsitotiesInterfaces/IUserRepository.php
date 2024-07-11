<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface IUserRepository
{
    public function profile(): ?User;
    public function getAllActiveUsers(Request $request): LengthAwarePaginator;
    public function getAllNotActiveUsers(Request $request): LengthAwarePaginator;
    public function getUserById(int $id): User;
    public function createUser(array $userData): User;
    public function updateUser(User $user, array $data): User;
    public function deactivateUser(User $user): User;
    public function reactivateUser(User $user): User;
    public function deleteUser(User $user): bool;
}
