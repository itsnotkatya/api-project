<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use http\Client\Request;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    use \HelperTrait;

    #[Route ("/user", name: "addTask", methods: ["POST"])]

    public function addTask(Request $request, EntityManagerInterface $entityManager, TaskRepository $taskRepository)
    {
        try {
            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('name') || !$request->request->get('description')) {
                throw new \Exception();
            }
            $post = new Post();
            $post->setName($request->get('name'));
            $post->setDescription($request->get('description'));
            $entityManager->persist($post);
            $entityManager->flush();

            $data = [
                'status' => Response::HTTP_OK,
                'message' => "Task added successfully",
            ];
            return $this->response($data);

        } catch (\Exception $e){
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => "Data no valid",
            ];
            return $this->response($data, 422);
        }

    }

    #[Route ("/task", name: "getTasks", methods: ["POST"])]

    public function getTasks(TaskRepository $taskRepository):JsonResponse
    {
        $data = $taskRepository->findAll();
        return $this->response($data);
    }

    #[Route ("/user", name: "getTask", methods: ["POST"])]

    public function getTask (TaskRepository $taskRepository, $id){
        $post = $taskRepository->find($id);

        if (!$post){
            $data = [
                'status' => 404,
                'errors' => "Post not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($post);
    }

    #[Route ("/user", name: "updateTask", methods: ["POST"])]

    public function updateTask(Request $request, EntityManagerInterface $entityManager, TaskRepository $taskRepository, $id){

        try{
            $post = $taskRepository->find($id);

            if (!$post){
                $data = [
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => "Task not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('name') || !$request->request->get('description')){
                throw new \Exception();
            }

            $post->setName($request->get('name'));
            $post->setDescription($request->get('description'));
            $entityManager->flush();

            $data = [
                'status' => Response::HTTP_OK,
                'message' => "Task updated successfully",
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }

    }

    public function deleteTask(EntityManagerInterface $entityManager, TaskRepository $taskRepository, $id){
        $post = $taskRepository->find($id);

        if (!$post){
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => "Task not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($post);
        $entityManager->flush();
        $data = [
            'status' => Response::HTTP_OK,
            'errors' => "Task deleted successfully",
        ];
        return $this->response($data);
    }
}
