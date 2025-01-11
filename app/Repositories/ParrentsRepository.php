<?php

namespace App\Repositories;

use App\Exceptions\ParrentControllerException;
use App\Interfaces\RepsitotiesInterfaces\ParrentsRepositoryInterface;
use App\Models\Parrent;
use App\Models\User;
use App\QueryFilters\FilterParrentsBy;
use App\QueryFilters\ParrentSearchBy;
use App\QueryFilters\ParrentSortBy;
use App\QueryFilters\UserSearchBy;
use App\QueryFilters\UserSortBy;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ParrentsRepository implements ParrentsRepositoryInterface
{

    /**
     * Retrieves and formats data for active parents based on the specified request.
     *
     * @param Request $request The incoming HTTP request containing filtering parameters.
     * @return LengthAwarePaginator Paginated list of formatted active parent data.
     * @throws ParrentControllerException If an error occurs while retrieving or formatting the data.
     */
    final public function getActiveParrents(Request $request): LengthAwarePaginator
    {
        try {
            return $this->formatParrentsData (true, $request);
        } catch (Exception $exception) {
            throw ParrentControllerException::getActiveParrentsError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    /**
     * Filters, processes, and formats parent data based on user activity and request parameters.
     *
     * @param bool $useractive Indicates whether to filter by active or inactive users.
     * @param Request $request The incoming HTTP request containing pagination and filtering parameters.
     * @return LengthAwarePaginator Paginated list of processed and formatted parent data.
     */
    private function formatParrentsData(bool $useractive, Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);
        $parrents = Parrent::whereHas('user', function ($query) use ($useractive) {
            $query->where('active', $useractive);
        })->with('user')->with('children_relations');
        $parrents = app(Pipeline::class)
            ->send($parrents)
            ->through([
                FilterParrentsBy::class,
                ParrentSearchBy::class,
                ParrentSortBy::class
            ])->thenReturn();

        if ($perPage !== 'all') {
            $parrents = $parrents->paginate((int)$perPage);
        } else {
            $parrents = $parrents->paginate($parrents->count());
        }
        return $parrents;
    }

    /**
     * Retrieves a list of active parents for selection purposes.
     *
     * @return Collection A collection of active parents.
     * @throws ParrentControllerException If an error occurs during data retrieval.
     */
    final public function getActiveParrentsForSelect(): Collection
    {
        try {
            return Parrent::whereHas('user', function ($query) {
                $query->where('active', true);
            })->get();
        } catch (Exception $exception) {
            throw ParrentControllerException::getActiveParrentsForSelectError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    /**
     * Retrieves a collection of active parents that can be selected for a given child,
     * excluding parents already related to the specified child.
     *
     * @param int $childId The ID of the child to filter parent relations.
     * @return Collection Collection of active parents available for selection.
     * @throws ParrentControllerException If an error occurs during the retrieval process.
     */
    final public function getActiveParrentsForUpdateSelect(int $childId): Collection
    {
        try {
            return Parrent::whereHas('user', function ($query) {
                $query->where('active', true);
            })->whereDoesntHave('children_relations', function ($query) use ($childId) {
                $query->where('child_id', $childId);
            })->get();
        } catch (Exception $exception) {
            throw ParrentControllerException::getActiveParrentsForSelectError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    /**
     * Retrieves and formats data for inactive parents based on the specified request.
     *
     * @param Request $request The incoming HTTP request containing filtering parameters.
     * @return LengthAwarePaginator Paginated list of formatted inactive parent data.
     * @throws ParrentControllerException If an error occurs while retrieving or formatting the data.
     */
    final public function getNotActiveParrents(Request $request): LengthAwarePaginator
    {
        try {
            return  $this->formatParrentsData( false, $request);
        } catch (Exception $exception) {
            throw ParrentControllerException::getNotActiveParrentsError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    /**
     * Retrieves a parent record by its unique identifier, including related children and user information.
     *
     * @param int $id The unique identifier of the parent.
     * @return Parrent|null The parent record with related data or null if not found.
     * @throws ParrentControllerException If the parent record is not found or an error occurs during retrieval.
     */
    final public function getParrentById(int $id): ?Parrent
    {
        try {
            $parrent = Parrent::where('id', $id)
                ->with('children_relations')
                ->with('user')
                ->first();
            if (!$parrent) throw ParrentControllerException::parrentNotFoundError($id);
            return $parrent;
        } catch (Exception $exception) {
            throw ParrentControllerException::parrentNotFoundError($id);
        }
    }

    /**
     * Creates a new parent record along with associated child relationship data.
     *
     * @param array $data Data for the new parent, including optional child relationship details.
     * @return Parrent The newly created parent instance.
     * @throws ParrentControllerException If an error occurs during the creation process or while syncing relationships.
     */
    final public function createParrent(array $data): Parrent
    {
        try {
            $relationsData = [];
            if (isset($data['children'])) {
                $relationsData = $data['children'];
                unset($relationsData['children']);
            }
            $newParrent = Parrent::create($data);
            if (!$newParrent) throw new Exception('Помилка створення батька');
            if (!empty($relationsData)) {
                array_walk($relationsData, function ($item) use (&$relationsData, &$syncData) {
                    $syncData[$item['child_id']] = ['relations' => $item['relations']];
                });
                $newParrent->children_relations()->sync($syncData);
            }
            return $newParrent;
        } catch (Exception $exception) {
            throw ParrentControllerException::createParrentError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    /**
     * Updates the given parent with provided data, including synchronizing child relationships.
     *
     * @param Parrent $parrent The parent entity to be updated.
     * @param array $data Data to update the parent entity with, including children relationships if provided.
     *
     * @return Parrent The updated parent entity.
     *
     * @throws ParrentControllerException If updating the parent or child relationships fails.
     */
    final public function updateParrent(Parrent $parrent, array $data): Parrent
    {
        try {
            $syncData = [];
            if (isset($data['children'])) {
                $relationsData = $data['children'];
                array_walk($relationsData, function ($item) use (&$relationsData, &$syncData) {
                    $syncData[$item['child_id']] = ['relations' => $item['relations']];
                });
                unset($data['children']);
            }
            $parrent->children_relations()->sync($syncData);
            if (!$parrent->update($data)) throw ParrentControllerException::updateParrentError(Response::HTTP_BAD_REQUEST);
            return $parrent;
        } catch (Exception $exception) {
            throw ParrentControllerException::updateParrentError($exception->getCode() === 0 ? 500 : $exception->getCode());
        }
    }

    /**
     * Deletes the specified parent entity.
     *
     * @param Parrent $parrent The parent entity to be deleted.
     *
     * @return bool True if the deletion was successful.
     *
     * @throws ParrentControllerException If the deletion fails.
     */
    final public function deleteParrent(Parrent $parrent): bool
    {
        try {
            if (!$parrent->delete()) throw ParrentControllerException::deleteParrentError($parrent->id);
            return true;
        } catch (Exception $exception) {
            throw ParrentControllerException::deleteParrentError($parrent->id);
        }
    }
}
