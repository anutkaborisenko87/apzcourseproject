<?php

namespace App\Repositories;

use App\Exceptions\PositionsControllerException;
use App\Interfaces\RepsitotiesInterfaces\IPositionsRepository;
use App\Models\Position;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class PositionsRepository implements IPositionsRepository
{

    final public function getPositions(): Collection
    {
        try {
            return Position::all();
        } catch (Exception $exception) {
            throw PositionsControllerException::getPositionsError($exception->getCode());
        }
    }

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

    final public function updatePositionInfo(Position $position, array $data): Position
    {
        try {
            if (!$position->update($data)) throw PositionsControllerException::updatePositionInfoError(Response::HTTP_BAD_REQUEST);
            return $position;
        } catch (Exception $exception) {
            throw PositionsControllerException::updatePositionInfoError($exception->getCode());
        }
    }

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
