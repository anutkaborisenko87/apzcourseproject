<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChildrenRequests\CreateChildRequest;
use App\Http\Requests\Api\ChildrenRequests\UpdateChildRequest;
use App\Interfaces\ServicesInterfaces\IChildrenService;
use Illuminate\Http\Request;
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

    final public function indexAllChildren(Request $request): Response
    {
        return response($this->childrenService->allChildrenList($request));
    }

    final public function indexForEnrolmentChildren(Request $request): Response
    {
        return response($this->childrenService->allChildrenForEnrolmentList($request));
    }

    final public function indexInTrainingChildren(Request $request): Response
    {
        return response($this->childrenService->allChildrenInTrainingList($request));
    }

    final public function indexGraduatedChildren(Request $request): Response
    {
        return response($this->childrenService->allGraduatedChildrenList($request));
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
