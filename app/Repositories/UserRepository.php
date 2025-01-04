<?php

namespace App\Repositories;

use App\Exceptions\UsersControllerException;
use App\Interfaces\RepsitotiesInterfaces\UserRepositoryInterface;
use App\Models\User;
use App\QueryFilters\DateFilterUsersBy;
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

    /**
     * Retrieves the user's profile.
     *
     * @return User|null
     */
    final public function profile(): ?User
    {
        return $this->user;
    }

    /**
     * Retrieves all active users excluding the currently authenticated user.
     *
     * @param Request $request The HTTP request instance containing filtering data.
     * @return LengthAwarePaginator
     */
    final public function getAllActiveUsers(Request $request): LengthAwarePaginator
    {
        $users = User::query();
        $users->where('id', '<>', $this->user->id)->where('active', true);

        return $this->getFilteredData($users, $request);
    }

    /**
     * Retrieves filtered and paginated user data based on request parameters.
     *
     * @param Builder $builder The query builder instance for filtering and sorting users.
     * @param Request $request The current HTTP request containing filtering and pagination parameters.
     *
     * @return LengthAwarePaginator Paginated and filtered user data.
     */
    private function getFilteredData(Builder $builder, Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);
        $users = app(Pipeline::class)
            ->send($builder)
            ->through([
                FilterUsersBy::class,
                DateFilterUsersBy::class,
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

    /**
     * Retrieves all users that are not active, excluding the current logged-in user.
     *
     * @param Request $request The HTTP request instance containing query parameters for filtering.
     * @return LengthAwarePaginator The paginated collection of inactive users.
     */
    final public function getAllNotActiveUsers(Request $request): LengthAwarePaginator
    {
        return $this->getFilteredData(User::where('id', '<>', $this->user->id)->where('active', false), $request);
    }

    /**
     * Retrieves the minimum birthdate of users based on their active status,
     * excluding the current logged-in user.
     *
     * @param bool $activeUser Determines whether to filter users by active or inactive status.
     * @return string The minimum birthdate of the filtered users.
     */
    final public function getMinBirthDateUsers(bool $activeUser): string
    {
        return User::where('id', '<>', $this->user->id)->where('active', $activeUser)->min('birth_date') ?? '';
    }

    /**
     * Retrieves the minimum birthdate of users based on their active status,
     * excluding the current logged-in user.
     *
     * @param bool $activeUser Determines whether to filter users by active or inactive status.
     * @return string The minimum birthdate of the filtered users.
     */
    final public function getMaxBirthDateUsers(bool $activeUser): string
    {
        return User::where('id', '<>', $this->user->id)->where('active', $activeUser)->max('birth_date') ?? '';
    }

    /**
     * Retrieves a user by their unique identifier.
     *
     * @param int $id The unique ID of the user to retrieve.
     * @return User The user instance associated with the provided ID.
     * @throws UsersControllerException If the user is not found or an exception occurs during retrieval.
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
     * Creates a new user with the provided data.
     *
     * @param array $userData An associative array containing the user data to be created.
     * @return User The newly created user instance.
     * @throws UsersControllerException If an error occurs during the user creation process.
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
     * Updates the specified user with the given data.
     *
     * @param User $user The user instance to be updated.
     * @param array $data The data to update the user with.
     * @return User The updated user instance.
     * @throws UsersControllerException If an error occurs while updating the user.
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
     * Deactivates a user by setting their active status to false.
     *
     * @param User $user The user instance to be deactivated.
     * @return User The updated user instance after deactivation.
     * @throws UsersControllerException If an error occurs during the update process.
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

    /**
     * Reactivates a user by setting their 'active' status to true.
     *
     * @param User $user The user instance to be reactivated.
     * @return User The user instance after reactivation.
     * @throws UsersControllerException When the update operation fails.
     */
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
     * Deletes a specified user from the system.
     *
     * @param User $user The user instance to be deleted.
     * @return bool True if the user was successfully deleted.
     * @throws UsersControllerException If an error occurs during the deletion process.
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
