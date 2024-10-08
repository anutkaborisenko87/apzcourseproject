<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ParrentsRequests\CreateParrentRequest;
use App\Http\Requests\Api\ParrentsRequests\UpdateParrentRequest;
use App\Interfaces\ServicesInterfaces\ParrentsServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParrentsController extends Controller
{
    private ParrentsServiceInterface $parrentsService;

    public function __construct(ParrentsServiceInterface $parrentsService)
    {
        $this->parrentsService = $parrentsService;
    }

    final public function indexActive(Request $request): Response
    {
        return response($this->parrentsService->getActiveParrentsList($request));
    }

    final public function indexForSelect(int $childId = null): Response
    {
        if (!is_null($childId)) return response($this->parrentsService->getParrentsListForUpdateSelect($childId));
        return response($this->parrentsService->getParrentsListForSelect());
    }

    final public function indexNotActive(Request $request): Response
    {
        return response($this->parrentsService->getNotActiveParrentsList($request));
    }

    final public function show(int $parrent): Response
    {
        return response($this->parrentsService->getParrentInfo($parrent));
    }

    final public function reactivate(int $parrent): Response
    {
        return response($this->parrentsService->reactivateParrent($parrent));
    }

    final public function deactivate(int $parrent): Response
    {
        return response($this->parrentsService->deactivateParrent($parrent));
    }

    final public function store(CreateParrentRequest $request): Response
    {
        return response($this->parrentsService->createNewParrent($request->validated()));
    }

    final public function update(UpdateParrentRequest $request, int $parrent): Response
    {
        return response($this->parrentsService->updateParrentInfo($parrent, $request->validated()));
    }

    final public function destroy(int $parrent): Response
    {
        return response($this->parrentsService->deleteParrentInfo($parrent));
    }


}
