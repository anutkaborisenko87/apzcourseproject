<?php

namespace App\Repositories;

use App\Interfaces\RepsitotiesInterfaces\IUserRpository;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class UserRepository implements IUserRpository
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    final public function profile(): ?User
    {
        $user = $this->user;
        return $user;
    }

    final public function getAllActiveUsers(): LengthAwarePaginator
    {
        return User::where('id', '<>', $this->user->id)->where('active', true)->paginate(10);
    }

    final public function getAllNotActiveUsers(): LengthAwarePaginator
    {
        return User::where('id', '<>', $this->user->id)->where('active', false)->paginate(10);
    }

    final public function getUserById(int $id): User
    {
        try {
            $user = User::find($id);
            if (!$user) throw new Exception('Користувача не знайдено');
            return $user;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function createUser(array $userData): User
    {
        try {
            return User::create($userData);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function updateUser(User $user, array $data): User
    {
        try {
            if (!$user->update($data)) throw new Exception('Невдала спроба оновити дані.');
            return $user;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
    final public function deactivateUser(User $user): User
    {
        try {
            if (!$user->update(['active' => false])) throw new Exception('Невдала спроба оновити дані.');
            return $user;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
    final public function reactivateUser(User $user): User
    {
        try {
            if (!$user->update(['active' => true])) throw new Exception('Невдала спроба оновити дані.');
            return $user;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
    final public function deleteUser(User $user): bool
    {
        try {
            if (!$user->delete()) throw new Exception('Невдала спроба видалити дані.');
            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
