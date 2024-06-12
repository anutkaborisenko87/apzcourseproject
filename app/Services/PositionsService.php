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
        try {
            return PositionResource::collection($this->positionsRepository->getPositions())->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function createPosition(array $data): array
    {
        try {
            return (new PositionResource($this->positionsRepository->createPositionInfo($data)))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function updatePosition(int $id, array $data): array
    {
        try {
            $position = $this->positionsRepository->getPositionInfo($id);
            return (new PositionResource($this->positionsRepository->updatePositionInfo($position, $data)))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function deletePosition(int $id): array
    {
        try {
            $position = $this->positionsRepository->getPositionInfo($id);
            $this->positionsRepository->deletePositionInfo($position);
            return (new PositionResource($position))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getPosition(int $positionId): array
    {
        try {
            $position = $this->positionsRepository->getPositionInfo($positionId);
            return (new PositionResource($position))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
