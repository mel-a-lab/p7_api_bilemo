<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Account;
use App\Service\UserService;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer) {}

    #[Route('/api/account/{account}/users', name: 'show_user', methods: ['GET'])]
    public function showUsers(UserService $userService, Account $account): JsonResponse
    {
        $users = $userService->showUsersForAccount($account);

        $context = SerializationContext::create();
        $jsonUsers = $this->serializer->serialize($users, 'json', $context);

        return new JsonResponse($jsonUsers, Response::HTTP_OK, [], true);
    }

    #[Route('/api/account/{idAccount}/users', name: 'registration_user', methods: ['POST'])]
    public function registration(Request $request, UserService $userService): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $result = $userService->registerUser($parameters);

        if (array_key_exists('errors', $result)) {
            return $this->json(['errors' => $result['errors']], Response::HTTP_BAD_REQUEST);
        }

        $context = SerializationContext::create();
        $jsonUser = $this->serializer->serialize($result['user'], 'json', $context);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(User $user, UserService $userService): JsonResponse
    {
        try {
            $userService->deleteUser($user);
            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->json(['message' => 'An error occurred'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/users/{id}', name: 'api_user_details', methods: ['GET'])]
    public function getUserDetails(User $user): JsonResponse
    {
        $context = SerializationContext::create();
        $jsonUser = $this->serializer->serialize($user, 'json', $context);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }
}
