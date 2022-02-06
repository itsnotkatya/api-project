<?php

namespace App\Controller;

use App\Exception\API\APIException;
use App\Form\UserType;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Traits\HelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/user', name: 'user')]
class UserController extends AbstractAPIController
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
     * @param Request $request
     * @param PasswordHasherFactoryInterface $hasherFactory
     *
     * @return JsonResponse
     *
     * @throws APIException
     */
    #[Route ('/register', name: "register", methods: ['POST'])]
    public function registerUserAction (Request $request, PasswordHasherFactoryInterface $hasherFactory): JsonResponse
    {
        $user = $this->handleForm($this->createForm(UserType::class), json_decode($request->getContent(), true));

        $user->setPassword(
            $hasherFactory->getPasswordHasher($user)->hash($user->getPassword())
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $data = [
            "data" => Response::HTTP_OK,
            "message" => "UserRegistered",
        ];

        return $this->response($data);
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }
}