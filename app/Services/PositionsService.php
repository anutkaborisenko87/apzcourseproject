<?php

namespace App\Services;

use App\Http\Resources\PositionResource;
use App\Interfaces\ServicesInterfaces\IPositionsService;
use App\Models\Position;
use App\Repositories\PositionsRepository;
use Exception;

class PositionsService implements IPositionsService
{
    private $positionsRepository;

    public function __construct(PositionsRepository $positionsRepository)
    {
        $this->positionsRepository = $positionsRepository;
    }

    final public function getPositionsList(): array
    {
        return PositionResource::collection($this->positionsRepository->getPositions())->resolve();
    }

    final public function createPosition(array $data): array
    {
        return (new PositionResource($this->positionsRepository->createPositionInfo($data)))->resolve();
    }

    final public function updatePosition(int $id, array $data): array
    {
        $position = $this->positionsRepository->getPositionInfo($id);
        return (new PositionResource($this->positionsRepository->updatePositionInfo($position, $data)))->resolve();
    }

    final public function deletePosition(int $id): array
    {
        $position = $this->positionsRepository->getPositionInfo($id);
        $this->positionsRepository->deletePositionInfo($position);
        return (new PositionResource($position))->resolve();
    }

    final public function getPosition(int $positionId): array
    {
        $position = $this->positionsRepository->getPositionInfo($positionId);
        return (new PositionResource($position))->resolve();
    }
}
