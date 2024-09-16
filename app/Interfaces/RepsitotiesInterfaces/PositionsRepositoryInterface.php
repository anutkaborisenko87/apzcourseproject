<?php

namespace App\Interfaces\RepsitotiesInterfaces;

use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;

interface PositionsRepositoryInterface
{
    public function getPositions(): Collection;
    public function createPositionInfo(array $data): Position;
    public function getPositionInfo(int $id): ?Position;
    public function updatePositionInfo(Position $position, array $data): Position;
    public function deletePositionInfo(Position $position): bool;
}
