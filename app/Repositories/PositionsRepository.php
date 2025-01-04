<?php

namespace App\Repositories;

use App\Exceptions\PositionsControllerException;
use App\Interfaces\RepsitotiesInterfaces\PositionsRepositoryInterface;
use App\Models\Position;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class PositionsRepository implements PositionsRepositoryInterface
{

    /**
     * Retrieve all positions from the database.
     *
     * @return Collection The collection of all positions.
     * @throws PositionsControllerException If an error occurs while fetching positions.
     */
    final public function getPositions(): Collection
    {
        try {
            return Position::all();
        } catch (Exception $exception) {
            throw PositionsControllerException::getPositionsError($exception->getCode());
        }
    }

    /**
     * Create a new position using the provided data.
     *
     * @param array $data The data used to create a new position.
     * @return Position The newly created position instance.
     * @throws PositionsControllerException If the position cannot be created or an error occurs.
     */
    final public function createPositionInfo(array $data): Position
    {
        try {
            $position = Position::create($data);
            if (!$position) throw PositionsControllerException::createPositionsError(Response::HTTP_BAD_REQUEST);
            return $position;
        } catch (Exception $exception) {
            throw PositionsControllerException::createPositionsError(Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Retrieves position information by its ID.
     *
     * @param int $id The ID of the position to retrieve.
     * @return Position|null Returns the Position object if found, or null if not found.
     * @throws PositionsControllerException When an error occurs while retrieving the position.
     */
    final public function getPositionInfo(int $id): ?Position
    {
        try {
            $position = Position::find($id);
            if (!$position) throw PositionsControllerException::getPositionInfoError($id);
            return $position;
        } catch (Exception $exception) {
            throw PositionsControllerException::getPositionInfoError($id);
        }
    }

    /**
     * Updates the information of a specified position.
     *
     * @param Position $position The position object to be updated.
     * @param array $data The data to update the position with.
     * @return Position Returns the updated Position object.
     * @throws PositionsControllerException When an error occurs during the update process.
     */
    final public function updatePositionInfo(Position $position, array $data): Position
    {
        try {
            if (!$position->update($data)) throw PositionsControllerException::updatePositionInfoError(Response::HTTP_BAD_REQUEST);
            return $position;
        } catch (Exception $exception) {
            throw PositionsControllerException::updatePositionInfoError($exception->getCode());
        }
    }

    /**
     * Deletes the provided position information.
     *
     * @param Position $position The position instance to be deleted.
     * @return bool Returns true if the position is successfully deleted.
     * @throws PositionsControllerException When an error occurs during deletion.
     */
    final public function deletePositionInfo(Position $position): bool
    {
        try {
            if (!$position->delete()) throw PositionsControllerException::deletePositionInfoError($position->id);
            return true;
        } catch (Exception $exception) {
            throw PositionsControllerException::deletePositionInfoError($position->id);
        }
    }
}
