<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Traits\HelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    use HelperTrait;

    #[Route ("/user", name: "addUser", methods: ["POST"])]

    public function addUser (Request $request, UserPasswordHasherInterface $passwordEncoder) {

        $request = $this->transformJsonBody($request); //приняли запрос и трпнсформировали тело в json
        $user = new User();
        $user->setName($request->get("name"));
        $user->setLogin($request->get("login"));
        $user->setPassword($passwordEncoder->hashPassword($user, $request->get("password")));

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();   //добавили в БД

        $data = [
            "data" => Response::HTTP_OK,
            "message" => "User added successfully",

        ];

        return $this->response($data);
    }

    #[Route ("/user", name: "getUser", methods: ["POST"])]

    public function getUser (UserRepository $userRepository, $id) {
        $post = $userRepository->find($id);

        if (!$post){
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => "User not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($post);
    }

    #[Route ("/user", name: "updateUser", methods: ["POST"])]

    public function updateUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, $id){

        try{
            $post = $userRepository->find($id);

            if (!$post){
                $data = [
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => "User not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('name') || !$request->request->get('login')){
                throw new \Exception();
            }

            $post->setName($request->get('name'));
            $post->setLogin($request->get('login'));
            $entityManager->flush();

            $data = [
                'status' => Response::HTTP_OK,
                'message' => "User updated successfully",
            ];
            return $this->response($data);

        } catch (\Exception $e){
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }

    }

    #[Route ("/user", name: "deleteUser", methods: ["POST"])]

    public function deleteUser(EntityManagerInterface $entityManager, UserRepository $userRepository, $id){
        $post = $userRepository->find($id);

        if (!$post){
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => "User not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($post);
        $entityManager->flush();
        $data = [
            'status' => Response::HTTP_OK,
            'message' => "User deleted successfully",
        ];
        return $this->response($data);
    }

}
