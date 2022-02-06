<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Exception\API\APIException;
use App\Form\TaskType;
use App\Traits\HelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('/task', name: 'task_')]
class TaskController extends AbstractAPIController
{
    use HelperTrait;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws APIException
     * @throws \JsonException
     */
    #[Route (name: "Add", methods: ["POST"])]
    public function addTask(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $request = $this->transformJsonBody($request);

        /** @var Task $task */
        $task = $this->handleForm(
            $this->createForm(TaskType::class),
            $request->request->all()
        );

        /** @var User $user */
        $user = $this->getUser();

        try {
            $user->addTask($task);
            $entityManager->persist($task);
            $entityManager->flush();

            $data = [
                'status' => Response::HTTP_OK,
                'message' => "Task added successfully",
            ];
            return $this->response($data);

        } catch (Exception $e){
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => "InvalidData",
            ];
            return $this->response($data, 422);
        }
    }

    #[Route ('/list', name: "GetByUser", methods: ["GET"])]
    public function getTasks(): JsonResponse
    {
        try{
            /** @var User $user */
            $user = $this->getUser();
            $tasks = $user->getTasks();

            $data = [];
            foreach ($tasks as $task){
                $data[] = [
                    "id"=>$task->getId(),
                    "status"=>$task->getStatus(),
                    "text"=>$task->getText(),
                    "date"=>$task->getDate()
                ];
            }

            return $this->response($data);
        } catch (Exception $e){
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => "Data no valid",
            ];
            return $this->response($data, 422);
        }
    }

    #[Route ('/{task}', name: "Get", methods: ["GET"])]
    public function getTask (Task $task): JsonResponse
    {
        return $this->response($task);
    }


    #[Route ('/{task}',name: "Update", methods: ["PUT"])]
    public function updateTask(Request $request, Task $task): JsonResponse
    {

        $request = $this->transformJsonBody($request);

        /** @var Task $task */
        $task = $this->handleForm(
            $this->createForm(TaskType::class, $task),
            $request->request->all()
        );
        try{
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $data = [
                'status' => Response::HTTP_OK,
                'message' => "Task updated successfully",
            ];
            return $this->response($data);

        }catch (Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }
    }

    #[Route ('/{task}',name: "Delete", methods: ["DELETE"])]
    public function deleteTask(EntityManagerInterface $entityManager, Task $task): JsonResponse
    {
        $entityManager->remove($task);
        $entityManager->flush();
        $data = [
            'status' => Response::HTTP_OK,
            'errors' => "Task deleted successfully",
        ];
        return $this->response($data);
    }
}
