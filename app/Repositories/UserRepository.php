<?php

namespace App\Repositories;

use App\Exceptions\UsersControllerException;
use App\Interfaces\RepsitotiesInterfaces\UserRepositoryInterface;
use App\Models\User;
use App\QueryFilters\FilterUsersBy;
use App\QueryFilters\UserSearchBy;
use App\QueryFilters\UserSortBy;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    final public function profile(): ?User
    {
        return $this->user;
    }

    final public function getAllActiveUsers(Request $request): LengthAwarePaginator
    {
        $users = User::query();
        $users->where('id', '<>', $this->user->id)->where('active', true);

        return $this->getFilteredData($users, $request);
    }

    private function getFilteredData(Builder $builder, Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);
        $users = app(Pipeline::class)
            ->send($builder)
            ->through([
                FilterUsersBy::class,
                UserSearchBy::class,
                UserSortBy::class
            ])->thenReturn();
        if ($perPage !== 'all') {
            $users = $users->paginate((int) $perPage);
        } else {
            $users = $users->paginate($users->count());
        }

        return $users;
    }

    final public function getAllNotActiveUsers(Request $request): LengthAwarePaginator
    {
        return $this->getFilteredData(User::where('id', '<>', $this->user->id)->where('active', false), $request);
    }

    final public function getBirthYearsUsers(bool $activeUser): Collection
    {
        return User::where('id', '<>', $this->user->id)->where('active', $activeUser)->distinct()->pluck('birth_year');
    }

    /**
     * @throws UsersControllerException
     */
    final public function getUserById(int $id): User
    {
        try {
            $user = User::find($id);
            if (!$user) throw UsersControllerException::getUserByIdError($id);
            return $user;
        } catch (Exception $exception) {
            throw UsersControllerException::getUserByIdError($id);
        }
    }

    /**
     * @throws UsersControllerException
     */
    final public function createUser(array $userData): User
    {
        try {
            return User::create($userData);
        } catch (Exception $exception) {
            throw UsersControllerException::createUserError($exception->getCode());
        }
    }

    /**
     * @throws UsersControllerException
     */
    final public function updateUser(User $user, array $data): User
    {
        try {
            if (!$user->update($data)) throw UsersControllerException::updateUserError(Response::HTTP_BAD_REQUEST);
            return $user;
        } catch (Exception $exception) {
            throw UsersControllerException::updateUserError($exception->getCode());
        }
    }

    /**
     * @throws UsersControllerException
     */
    final public function deactivateUser(User $user): User
    {
        try {
            if (!$user->update(['active' => false])) throw UsersControllerException::updateUserError(Response::HTTP_BAD_REQUEST);
            return $user;
        } catch (Exception $exception) {
            throw UsersControllerException::updateUserError($exception->getCode());
        }
    }
    final public function reactivateUser(User $user): User
    {
        try {
            if (!$user->update(['active' => true])) throw UsersControllerException::updateUserError(Response::HTTP_BAD_REQUEST);
            return $user;
        } catch (Exception $exception) {
            throw UsersControllerException::updateUserError($exception->getCode());
        }
    }

    /**
     * @throws UsersControllerException
     */
    final public function deleteUser(User $user): bool
    {
        try {
            if (!$user->delete()) throw UsersControllerException::deleteUserError(Response::HTTP_BAD_REQUEST);
            return true;
        } catch (Exception $exception) {
            throw UsersControllerException::deleteUserError($exception->getCode());
        }
    }
}
