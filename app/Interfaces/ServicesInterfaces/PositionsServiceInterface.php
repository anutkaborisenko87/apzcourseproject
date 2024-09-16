<?php

namespace App\Interfaces\ServicesInterfaces;

interface PositionsServiceInterface
{
    public function getPositionsList(): array;
    public function createPosition(array $data): array;
    public function updatePosition(int $id, array $data): array;
    public function deletePosition(int $id): array;
    public function getPosition(int $positionId): array;
}
