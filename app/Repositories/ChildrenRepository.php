<?php

namespace App\Repositories;

use App\Exceptions\ChildrenControllerException;
use App\Interfaces\RepsitotiesInterfaces\ChildrenRepositoryInterface;
use App\Models\Children;
use App\Models\User;
use App\QueryFilters\ChildSearchBy;
use App\QueryFilters\ChildSortBy;
use App\QueryFilters\UserSearchBy;
use App\QueryFilters\UserSortBy;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ChildrenRepository implements ChildrenRepositoryInterface
{

    /**
     * Retrieves a collection of all children with their associated user data for use in selection fields.
     *
     * Fetches all records of children along with their related user information.
     *
     * @return Collection A collection containing children data along with their associated user information.
     *
     * @throws ChildrenControllerException Thrown when an error occurs while retrieving the children list.
     */
    final public function getChildrenForSelect(): Collection
    {
        try {
            return Children::with('user')->get();
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    /**
     * Retrieves a collection of children available for updating a specific parent's selection.
     *
     * Filters children who are not associated with the given parent relation through `parrent_relations`.
     * Loads the 'user' relationship for each child.
     *
     * @param int $parrentId The ID of the parent to be excluded in the selection query.
     *
     * @return Collection A collection of children data available for selection.
     *
     * @throws ChildrenControllerException Thrown when an error occurs while retrieving the children list.
     */
    final public function getChildrenForUpdateSelect(int $parrentId): Collection
    {
        try {
            return Children::with('user')->whereDoesntHave('parrent_relations', function ($query) use ($parrentId) {
                $query->where('parrent_id', $parrentId);
            })->get();
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }


    /**
     * Retrieves a collection of children available for group selection.
     *
     * Fetches children who either do not have a group assigned or do not have a graduation date set.
     * Includes the related user information for each child.
     *
     * @return Collection A collection of children data.
     *
     * @throws ChildrenControllerException Thrown when an error occurs while retrieving the children list.
     */
    final public function getChildrenForGroupSelect(): Collection
    {
        try {
            return Children::with('user')
                ->whereNull('group_id')
                ->orWhereNull('graduation_date')
                ->get();
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    /**
     * Retrieves a paginated list of all children.
     *
     * Fetches all children records from the database without applying any specific filters.
     *
     * @param Request $request The incoming HTTP request object containing query parameters.
     *
     * @return LengthAwarePaginator A paginated collection of all children data.
     *
     * @throws ChildrenControllerException Thrown when an error occurs while retrieving the children list.
     */
    final public function getAllChildrenList(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatData(Children::query(), $request);
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    /**
     * Retrieves a paginated list of all children eligible for enrollment.
     *
     * Filters children who either do not have an enrollment date or are not assigned to any group.
     *
     * @param Request $request The incoming HTTP request object containing query parameters.
     *
     * @return LengthAwarePaginator A paginated collection of children data.
     *
     * @throws ChildrenControllerException Thrown when an error occurs while retrieving the children list.
     */
    final public function getAllChildrenForEnrollment(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatData(Children::whereNull('enrollment_date')->orWhereNull('group_id'), $request);
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    /**
     * Retrieves a paginated list of all children currently in training.
     *
     * Filters children who have both an enrollment date and a group assigned.
     * Excludes children who have a graduation date in the past or who are not yet assigned a graduation date.
     *
     * @param Request $request The incoming HTTP request object containing query parameters.
     *
     * @return LengthAwarePaginator A paginated collection of children data.
     *
     * @throws ChildrenControllerException Thrown when an error occurs while retrieving the children list.
     */
    final public function getAllChildrenInTraining(Request $request): LengthAwarePaginator
    {
        try {
            $today = date('Y-m-d');
            return $this->formatData(Children::whereNotNull('enrollment_date')
                ->whereNotNull('group_id')
                ->where(function ($query) use ($today) {
                    $query->whereDate('graduation_date', '>', $today);
                    $query->orWhereNull('graduation_date');
                }), $request);
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }
    }

    /**
     * Retrieves a paginated list of all graduated children.
     *
     * Filters children who have an enrollment date and whose graduation date is in the past.
     *
     * @param Request $request The incoming HTTP request object containing query parameters.
     *
     * @return LengthAwarePaginator A paginated collection of graduated children data.
     *
     * @throws ChildrenControllerException Thrown when an error occurs while retrieving the graduated children list.
     */
    final public function getAllGraduatedChildren(Request $request): LengthAwarePaginator
    {
        try {
            $today = date('Y-m-d');
            return $this->formatData(Children::whereNotNull('enrollment_date')
                ->whereDate('graduation_date', '<', $today), $request);
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenListError();
        }

    }

    /**
     * Formats and paginates a query result of children data based on the provided request parameters.
     *
     * Applies filtering and sorting to the query using specified pipelines. Supports both standard pagination
     * and a mode where all records are included in the result if 'per_page' is set to 'all'.
     *
     * @param mixed $childrenQuery The query builder instance for retrieving children data.
     * @param Request $request The incoming HTTP request object containing pagination and filtering options.
     *
     * @return LengthAwarePaginator A paginated collection of children data with applied filters and sorting.
     */
    private function formatData($childrenQuery, Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);

        $childrenList = app(Pipeline::class)
            ->send($childrenQuery->with('user'))
            ->through([
                ChildSearchBy::class,
                ChildSortBy::class
            ])->thenReturn();
        if ($perPage !== 'all') {
            $childrenList =  $childrenList->paginate((int) $perPage);
        } else {
            $childrenList =  $childrenList->paginate($childrenList->count());
        }
        return $childrenList;
    }

    /**
     * Retrieves the details of a child based on their unique identifier.
     *
     * Fetches a child along with their related parent relations, assigned group, and associated user data.
     *
     * @param int $childId The unique identifier of the child to be retrieved.
     *
     * @return Children The child data including related information.
     *
     * @throws ChildrenControllerException Thrown when the child with the specified ID is not found.
     */
    final public function getChildById(int $childId): Children
    {
        try {
            return Children::where('id', $childId)
                ->with('parrent_relations')
                ->with('group')
                ->with('user')->first();
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenNotFoundError($childId);
        }
    }

    /**
     * Creates a new child record along with associated parental relations.
     *
     * Handles input data to create a child entry in the database, including syncing parental relations
     * if provided in the input. If creation or association fails, it throws an exception.
     *
     * @param array $data The input data array containing child details, including optional parents data.
     *
     * @return Children The created child record.
     *
     * @throws ChildrenControllerException Thrown when an error occurs while creating the child record.
     */
    final public function createChildInfo(array $data): Children
    {
        try {
            $relationsData = [];
            if (isset($data['parrents'])) {
                $relationsData = $data['parrents'];
                unset($data['parrents']);
            }
            $cteatedChild = Children::create($data);
            if (!$cteatedChild) throw new Exception('Помилка створення інформації про дитину');
            if (!empty($relationsData)) {
                $syncData = [];
                array_walk($relationsData, function ($item) use (&$syncData) {
                    $syncData[$item['parrent_id']] = ['relations' => $item['relations']];
                });
                $cteatedChild->parrent_relations()->sync($syncData);
            }
            return $cteatedChild;
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenNotCreatedError();
        }
    }

    /**
     * Updates the information of a specified child, including their associated parent relations.
     *
     * Updates the child record with the provided data. If parent relationship data is provided,
     * it synchronizes the parent-child relations as well.
     *
     * @param Children $child The child entity whose information is to be updated.
     * @param array $data The array of updated data, which may include parent relationships.
     *
     * @return Children The updated child entity.
     *
     * @throws ChildrenControllerException Thrown when an error occurs while updating the child's information.
     */
    final public function updateChildInfo(Children $child, array $data): Children
    {
        try {
            $relationsData = [];
            if (isset($data['parrents'])) {
                $relationsData = $data['parrents'];
                unset($data['parrents']);
            }
            if (!$child->update($data)) throw new Exception('Помилка створення інформації про дитину');
            $syncData = [];
            array_walk($relationsData, function ($item) use (&$syncData) {
                $syncData[$item['parrent_id']] = ['relations' => $item['relations']];
            });
            $child->parrent_relations()->sync($syncData);
            return $child;
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenNotUpdatedError();
        }
    }

    /**
     * Deletes information about a specific child from the system.
     *
     * Ensures the child record is deleted from the database. Throws an exception if the deletion fails.
     *
     * @param Children $child The child entity to be deleted.
     *
     * @return bool Returns true if the deletion is successful.
     *
     * @throws ChildrenControllerException Thrown if the child record could not be deleted.
     */
    final public function  deleteChildInfo(Children $child): bool
    {
        try {
            if (!$child->delete())  throw ChildrenControllerException::childrenNotDeletedError($child->id);
            return true;
        } catch (Exception $exception) {
            throw ChildrenControllerException::childrenNotDeletedError($child->id);
        }
    }
}
