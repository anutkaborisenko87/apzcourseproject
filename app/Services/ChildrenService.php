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

    final public function allChildrenList(): array
    {
        try {
            return ChildrenResource::collection($this->childrenRepository->getAllChildrenList())->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function allChildrenForEnrolmentList(): array
    {
        try {
            return ChildrenResource::collection($this->childrenRepository->getAllChildrenForEnrollment())->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function allChildrenInTrainingList(): array
    {
        try {
            return ChildrenResource::collection($this->childrenRepository->getAllChildrenInTraining())->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function allGraduatedChildrenList(): array
    {
        try {
            return ChildrenResource::collection($this->childrenRepository->getAllGraduatedChildren())->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    final public function getChildInfo(int $childId): array
    {
        try {
            return (new ChildrenResource($this->childrenRepository->getChildById($childId)))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
    final public function addChildInfo(array $data): array
    {
        try {
            if (!isset($data['user'])) throw new Exception('Відсутні дані для створення');
            $childData = $data['child'] ?? [];
            $createdUser = $this->userRepository->createUser($data['user']);
            if (!$createdUser) throw new Exception('Помилка створення користувача');
            $childData['user_id'] = $createdUser->id;
            if (isset($childData['enrollment_date'])) {
                $childData['enrollment_year'] =  (new DateTime($childData['enrollment_date']))->format('Y');
            }
            if (isset($childData['graduation_date'])) {
                $childData['graduation_year'] =  (new DateTime($childData['graduation_date']))->format('Y');
            }
            $createdChild = $this->childrenRepository->createChildInfo($childData);
            return (new ChildrenResource($createdChild))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
    final public function updateChildInfo(int $childId, array $data): array
    {
        try {
            $childToUpdate = $this->childrenRepository->getChildById($childId);
            if (!$childToUpdate) throw new Exception('Дитина не знайдена');
            if (isset($data['user'])) {
               $this->userRepository->updateUser($childToUpdate->user, $data['user']);
            }
            $childData = $data['child'];
            if (isset($childData['enrollment_date'])) {
                $childData['enrollment_year'] =  (new DateTime($childData['enrollment_date']))->format('Y');
            }
            if (isset($childData['graduation_date'])) {
                $childData['graduation_year'] =  (new DateTime($childData['graduation_date']))->format('Y');
            }
            $updatedtedChild = $this->childrenRepository->updateChildInfo($childToUpdate, $childData);
            return (new ChildrenResource($updatedtedChild))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
    final public function deleteChildInfo(int $childId): array
    {
        try {
            $childToDelete = $this->childrenRepository->getChildById($childId);
            if (!$childToDelete) throw new Exception('Дитина не знайдена');
            $this->childrenRepository->deleteChildInfo($childToDelete);
            return (new ChildrenResource($childToDelete))->resolve();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
