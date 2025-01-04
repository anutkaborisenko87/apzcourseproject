<?php

namespace App\Services;

use App\Http\Resources\ChildrenForSelectResource;
use App\Http\Resources\ChildrenResource;
use App\Interfaces\RepsitotiesInterfaces\ChildrenRepositoryInterface;
use App\Interfaces\ServicesInterfaces\ChildrenServiceInterface;
use App\Interfaces\ServicesInterfaces\UserServiceInterface;
use Exception;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ChildrenService implements ChildrenServiceInterface
{
    private ChildrenRepositoryInterface $childrenRepository;
    private UserServiceInterface $userService;

    public function __construct(ChildrenRepositoryInterface $childrenRepository, UserServiceInterface $userService)
    {
        $this->childrenRepository = $childrenRepository;
        $this->userService = $userService;
    }

    /**
     * Retrieves a list of children formatted for selection purposes.
     *
     * @return array The resolved collection of children data suitable for a select list.
     */
    final public function childrenForSelectList(): array
    {
        return ChildrenForSelectResource::collection($this->childrenRepository->getChildrenForSelect())->resolve();
    }

    /**
     * Retrieves a list of children for update select based on the provided parent ID.
     *
     * @param int $parrenId The ID of the parent to retrieve the children for.
     * @return array An array of children formatted for select input.
     */
    final public function childrenForUpdateSelectList(int $parrenId): array
    {
        return ChildrenForSelectResource::collection($this->childrenRepository->getChildrenForUpdateSelect($parrenId))->resolve();
    }

    /**
     * Retrieves a list of children formatted for group selection.
     *
     * @return array An array of children data prepared for group select input.
     */
    final public function childrenForGroupSelectList(): array
    {
        return ChildrenForSelectResource::collection($this->childrenRepository->getChildrenForGroupSelect())->resolve();
    }

    /**
     * Retrieves the list of all children from the repository and formats the response data.
     *
     * @param Request $request Incoming HTTP request object containing necessary parameters.
     * @return array Formatted response data containing the list of children.
     */
    final public function allChildrenList(Request $request): array
    {
        $repoList = $this->childrenRepository->getAllChildrenList($request);
        return $this->formatRespData($repoList, $request);
    }

    /**
     * Retrieves a list of all children available for enrollment and formats the response.
     *
     * @param Request $request The HTTP request instance containing the necessary parameters.
     *
     * @return array An array of formatted response data for all children eligible for enrollment.
     */
    final public function allChildrenForEnrolmentList(Request $request): array
    {
        $repoList = $this->childrenRepository->getAllChildrenForEnrollment($request);
        return $this->formatRespData($repoList, $request);
    }

    /**
     * Retrieves a list of all children currently in training and formats the response data.
     *
     * @param Request $request The HTTP request instance containing the necessary parameters.
     *
     * @return array An array of formatted response data for all children in training.
     */
    final public function allChildrenInTrainingList(Request $request): array
    {
        $repoList = $this->childrenRepository->getAllChildrenInTraining($request);
        return $this->formatRespData($repoList, $request);
    }

    /**
     * Retrieves a list of all graduated children and formats the response data.
     *
     * @param Request $request The HTTP request instance.
     * @return array The formatted response data containing the list of graduated children.
     */
    final public function allGraduatedChildrenList(Request $request): array
    {
        $repoList = $this->childrenRepository->getAllGraduatedChildren($request);
        return $this->formatRespData($repoList, $request);
    }

    /**
     * Formats paginated children data into a structured array response.
     *
     * @param LengthAwarePaginator $childrenPaginated The paginated collection of children data.
     * @param Request $request The HTTP request instance containing input parameters.
     * @return array The structured array combining paginated data and request data.
     */
    private function formatRespData(LengthAwarePaginator $childrenPaginated, Request $request): array
    {
        $childrenResp = $childrenPaginated->toArray();
        $childrenResp['data'] = ChildrenResource::collection($childrenPaginated->getCollection())->resolve();
        $requestData = $request->except(['page', 'per_page']);
        return array_merge($childrenResp, $requestData);
    }

    /**
     * Retrieves detailed information for a specific child and formats it using the ChildrenResource.
     *
     * @param int $childId The unique identifier of the child.
     * @return array The formatted detailed information of the specified child.
     */
    final public function getChildInfo(int $childId): array
    {
        return (new ChildrenResource($this->childrenRepository->getChildById($childId)))->resolve();
    }

    final public function addChildInfo(array $data): array
    {
        if (!isset($data['user'])) throw new Exception('Відсутні дані для створення');
        $childData = $data['child'] ?? [];
        $createdUser = $this->userService->createUser($data['user']);
        if (!$createdUser) throw new Exception('Помилка створення користувача');
        $childData['user_id'] = $createdUser->id;
        if (isset($childData['enrollment_date'])) {
            $childData['enrollment_year'] = (new DateTime($childData['enrollment_date']))->format('Y');
        }
        if (isset($childData['graduation_date'])) {
            $childData['graduation_year'] = (new DateTime($childData['graduation_date']))->format('Y');
        }
        $createdChild = $this->childrenRepository->createChildInfo($childData);
        return (new ChildrenResource($createdChild))->resolve();
    }

    final public function updateChildInfo(int $childId, array $data): array
    {
        $childToUpdate = $this->childrenRepository->getChildById($childId);
        if (!$childToUpdate) throw new Exception('Дитина не знайдена');
        if (isset($data['user'])) {
            $this->userService->updateUser($childToUpdate->user_id, $data['user']);
        }
        $childData = $data['child'] ?? [];
        if (isset($childData['enrollment_date'])) {
            $childData['enrollment_year'] = (new DateTime($childData['enrollment_date']))->format('Y');
        }
        if (isset($childData['graduation_date'])) {
            $childData['graduation_year'] = (new DateTime($childData['graduation_date']))->format('Y');
        }
        $updatedtedChild = $this->childrenRepository->updateChildInfo($childToUpdate, $childData);
        return (new ChildrenResource($updatedtedChild))->resolve();
    }

    final public function deleteChildInfo(int $childId): array
    {
        $childToDelete = $this->childrenRepository->getChildById($childId);
        if (!$childToDelete) throw new Exception('Дитина не знайдена');
        $this->childrenRepository->deleteChildInfo($childToDelete);
        return (new ChildrenResource($childToDelete))->resolve();
    }
}
