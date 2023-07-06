<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/api/account/{idAccount}/users', name: 'show_user', methods: ['GET'])]
    public function showUser(UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findAll();
        return $this->json($user, 200);
    }

    #[Route('/api/account/{idAccount}/users', name: 'registration_user', methods: ['POST'])]
    public function registration(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): JsonResponse {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters);

        if ($form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('password')->getData()));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json($user, 201);
        }

        return $this->json(['message' => "Something is wrong with your properties"], 400);
    }

    #[Route('/api/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(null, 204);
    }

    #[Route('/api/users/{id}', name: 'api_user_details', methods: ['GET'])]
    public function getUserDetails(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = [
            'id' => $user->getId(),
            'name' => $user->getEmail(),
            'account' => $user->getAccount(),
        ];

        return $this->json($response);
    }


}
