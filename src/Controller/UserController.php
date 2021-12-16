<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Traits\HelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/user', name: 'user')]
class UserController extends AbstractController
{
    use HelperTrait;

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/user", methods={"POST"})
     */
    #[Route (name: "Add", methods: ['POST'])]
    public function addUser (Request $request, UserPasswordHasherInterface $passwordEncoder) {
        $request = $this->transformJsonBody($request); //приняли запрос и трпнсформировали тело в json
        $user = new User();
        $user->setName($request->get("name"));
        $user->setLogin($request->get("login"));
        $user->setPassword(
            $passwordEncoder->hashPassword($user, $request->get("password"))
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();   //добавили в БД

        $data = [
            "data" => Response::HTTP_OK,
            "message" => "User added successfully",
        ];
        return $this->response($data);
    }
}
