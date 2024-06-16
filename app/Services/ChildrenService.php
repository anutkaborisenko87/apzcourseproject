<?php

namespace App\Services;

use App\Http\Resources\ChildrenForSelectResource;
use App\Interfaces\ServicesInterfaces\IChildrenService;
use App\Repositories\ChildrenRepository;
use Exception;

class ChildrenService implements IChildrenService
{
    private $childrenRepository;

    public function __construct(ChildrenRepository $childrenRepository)
    {
        $this->childrenRepository = $childrenRepository;
    }
    final public function childrenForSelectList(): array
    {
        try {
            return ChildrenForSelectResource::collection($this->childrenRepository->getChildrenForSelect())->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function childrenForUpdateSelectList(int $parrenId): array
    {
        try {
            return ChildrenForSelectResource::collection($this->childrenRepository->getChildrenForUpdateSelect($parrenId))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
