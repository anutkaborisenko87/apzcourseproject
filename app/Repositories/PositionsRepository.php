<?php

namespace App\Repositories;

use App\Interfaces\RepsitotiesInterfaces\IPositionsRepository;
use App\Models\Position;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PositionsRepository implements IPositionsRepository
{

    final public function getPositions(): Collection
    {
        try {
            return Position::all();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function createPositionInfo(array $data): Position
    {
        try {
            $position = Position::create($data);
            if (!$position) throw new Exception('Помилка створення позиції');
            return $position;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getPositionInfo(int $id): ?Position
    {
        try {
            $position = Position::find($id);
            if (!$position) throw new Exception('Така позиція не знайдена');
            return $position;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function updatePositionInfo(Position $position, array $data): Position
    {
        try {
            if (!$position->update($data)) throw new Exception('Помилка оновлдення позиції');
            return $position;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function deletePositionInfo(Position $position): bool
    {
        try {
            if (!$position->delete()) throw new Exception('Помилка видалення позиції');
            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
