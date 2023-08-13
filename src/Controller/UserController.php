<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Account;
use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/api/account/{account}/users', name: 'show_user', methods: ['GET'])]
    public function showUsers(UserRepository $userRepository, Request $request, Account $account, PaginatorInterface $paginator, SerializerInterface $serialize): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $pagination = $paginator->paginate(
            $userRepository->findAll(),
            $page,
            $limit
        );

        $users = $userRepository->findBy(['account' => $account]);

        $context = SerializationContext::create();
        $jsonUsers = $serialize->serialize($users, 'json', $context);
        return new JsonResponse($jsonUsers, Response::HTTP_OK, ['accept' => 'json'], true);

        //  return $this->json($users, 200);
    }

    #[Route('/api/account/{idAccount}/users', name: 'registration_user', methods: ['POST'])]
    public function registration(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher, SerializerInterface $serialize,
        ValidatorInterface $validator
    ): JsonResponse {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters);

        $errors = $validator->validate($user);
    if (count($errors) > 0) {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
    }

        if ($form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('password')->getData()));

            $entityManager->persist($user);
            $entityManager->flush();

            // return $this->json($user, 201);
            $context = SerializationContext::create();
            $jsonUser = $serialize->serialize($user, 'json', $context);
            return new JsonResponse($jsonUser, Response::HTTP_CREATED, ['accept' => 'json'], true);
        }
    }

    #[Route('/api/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager, SerializerInterface $serialize): JsonResponse
    {
        if (!$user) {
            return new JsonResponse(['message' => "Something is wrong with your properties"], 404);
        } else {
            $entityManager->remove($user);
            $entityManager->flush();

            return $this->json(null, 204,);
        }
    }

    #[Route('/api/users/{id}', name: 'api_user_details', methods: ['GET'])]
    public function getUserDetails(User $user, EntityManagerInterface $entityManager, SerializerInterface $serialize): JsonResponse
    {
        $context = SerializationContext::create();
        $jsonUser = $serialize->serialize($user, 'json', $context);
        return new JsonResponse($jsonUser, Response::HTTP_OK, ['accept' => 'json'], true);
    }

}