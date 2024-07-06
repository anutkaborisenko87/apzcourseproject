<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChildrenRequests\CreateChildRequest;
use App\Http\Requests\Api\ChildrenRequests\UpdateChildRequest;
use App\Interfaces\ServicesInterfaces\IChildrenService;
use Illuminate\Http\Response;

class ChildrenController extends Controller
{
    private IChildrenService $childrenService;

    public function __construct(IChildrenService $childrenService)
    {
        $this->childrenService = $childrenService;
    }

    final public function indexForSelect(): Response
    {
        return response($this->childrenService->childrenForSelectList());
    }

    final public function indexForGroupSelect(): Response
    {
        return response($this->childrenService->childrenForGroupSelectList());
    }

    final public function indexForUpdateSelect(int $parrentId): Response
    {
        return response($this->childrenService->childrenForUpdateSelectList($parrentId));

    }

    final public function indexAllChildren(): Response
    {
        return response($this->childrenService->allChildrenList());
    }

    final public function indexForEnrolmentChildren(): Response
    {
        return response($this->childrenService->allChildrenForEnrolmentList());
    }

    final public function indexInTrainingChildren(): Response
    {
        return response($this->childrenService->allChildrenInTrainingList());
    }

    final public function indexGraduatedChildren(): Response
    {
        return response($this->childrenService->allGraduatedChildrenList());
    }

    final public function showChild(int $childId): Response
    {
        return response($this->childrenService->getChildInfo($childId));
    }

    final public function createChild(CreateChildRequest $request): Response
    {
        return response($this->childrenService->addChildInfo($request->validated()));
    }

    final public function updateChild(UpdateChildRequest $request, int $child): Response
    {
        return response($this->childrenService->updateChildInfo($child, $request->validated()));
    }

    final public function deleteChild(int $child): Response
    {
        return response($this->childrenService->deleteChildInfo($child));
    }
}
