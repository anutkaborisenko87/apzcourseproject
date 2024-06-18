<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChildrenRequests\CreateChildRequest;
use App\Http\Requests\Api\ChildrenRequests\UpdateChildRequest;
use App\Services\ChildrenService;
use Exception;
use Illuminate\Http\Response;

class ChildrenController extends Controller
{
    private $childrenService;

    public function __construct(ChildrenService $childrenService)
    {
        $this->childrenService = $childrenService;
    }

    final public function indexForSelect(): Response
    {
        try {
            return response($this->childrenService->childrenForSelectList());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function indexForUpdateSelect(int $parrentId): Response
    {
        try {
            return response($this->childrenService->childrenForUpdateSelectList($parrentId));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function indexAllChildren(): Response
    {
        try {
            return response($this->childrenService->allChildrenList());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function indexForEnrolmentChildren(): Response
    {
        try {
            return response($this->childrenService->allChildrenForEnrolmentList());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function indexInTrainingChildren(): Response
    {
        try {
            return response($this->childrenService->allChildrenInTrainingList());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function indexGraduatedChildren(): Response
    {
        try {
            return response($this->childrenService->allGraduatedChildrenList());
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function showChild(int $childId): Response
    {
        try {
            return response($this->childrenService->getChildInfo($childId));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function createChild(CreateChildRequest $request): Response
    {
        try {
            return response($this->childrenService->addChildInfo($request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function updateChild(UpdateChildRequest $request, int $child): Response
    {
        try {
            return response($this->childrenService->updateChildInfo($child, $request->validated()));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }

    final public function deleteChild(int $child): Response
    {
        try {
            return response($this->childrenService->deleteChildInfo($child));
        } catch (Exception $exception) {
            return response(['error' => $exception->getMessage()], 400);
        }
    }
}
