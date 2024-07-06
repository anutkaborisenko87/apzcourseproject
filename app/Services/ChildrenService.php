<?php

namespace App\Services;

use App\Http\Resources\ChildrenForSelectResource;
use App\Http\Resources\ChildrenResource;
use App\Interfaces\ServicesInterfaces\IChildrenService;
use App\Repositories\ChildrenRepository;
use App\Repositories\UserRepository;
use Exception;
use DateTime;

class ChildrenService implements IChildrenService
{
    private $childrenRepository;
    private $userRepository;

    public function __construct(ChildrenRepository $childrenRepository, UserRepository $userRepository)
    {
        $this->childrenRepository = $childrenRepository;
        $this->userRepository = $userRepository;
    }

    final public function childrenForSelectList(): array
    {
        return ChildrenForSelectResource::collection($this->childrenRepository->getChildrenForSelect())->resolve();
    }

    final public function childrenForUpdateSelectList(int $parrenId): array
    {
        return ChildrenForSelectResource::collection($this->childrenRepository->getChildrenForUpdateSelect($parrenId))->resolve();
    }

    final public function childrenForGroupSelectList(): array
    {
        return ChildrenForSelectResource::collection($this->childrenRepository->getChildrenForGroupSelect())->resolve();
    }

    final public function allChildrenList(): array
    {
        $repoList = $this->childrenRepository->getAllChildrenList();
        $resp = $repoList->toArray();
        $resp['data'] = ChildrenResource::collection($repoList->getCollection())->resolve();
        return $resp;
    }

    final public function allChildrenForEnrolmentList(): array
    {
        $repoList = $this->childrenRepository->getAllChildrenForEnrollment();
        $resp = $repoList->toArray();
        $resp['data'] = ChildrenResource::collection($repoList->getCollection())->resolve();
        return $resp;
    }

    final public function allChildrenInTrainingList(): array
    {
        $repoList = $this->childrenRepository->getAllChildrenInTraining();
        $resp = $repoList->toArray();
        $resp['data'] = ChildrenResource::collection($repoList->getCollection())->resolve();
        return $resp;
    }

    final public function allGraduatedChildrenList(): array
    {
        $repoList = $this->childrenRepository->getAllGraduatedChildren();
        $resp = $repoList->toArray();
        $resp['data'] = ChildrenResource::collection($repoList->getCollection())->resolve();
        return $resp;
    }

    final public function getChildInfo(int $childId): array
    {
        return (new ChildrenResource($this->childrenRepository->getChildById($childId)))->resolve();
    }

    final public function addChildInfo(array $data): array
    {
        if (!isset($data['user'])) throw new Exception('Відсутні дані для створення');
        $childData = $data['child'] ?? [];
        $createdUser = $this->userRepository->createUser($data['user']);
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
            $this->userRepository->updateUser($childToUpdate->user, $data['user']);
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
