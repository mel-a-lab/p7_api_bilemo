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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/api/account/{idAccount}/users', name: 'show_user', methods: ['GET'])]
    public function showUser(UserRepository $userRepository, int $idAccount): JsonResponse
    {
        $users = $userRepository->findBy(['account' => $idAccount]);

        return $this->json($users, 200);
    }

    #[Route('/api/account/{idAccount}/users', name: 'registration_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
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
        if (!$user) {
            return new JsonResponse(['message' => "Something is wrong with your properties"], 404);
        } else {
            $entityManager->remove($user);
            $entityManager->flush();

            return $this->json(null, 204);
        }
    }

    #[Route('/api/users/{id}', name: 'api_user_details', methods: ['GET'])]
    public function getUserDetails(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$user) {
            return new JsonResponse(['message' => "Something is wrong with your properties"], 404);
        } else {
            $response = [
                'id' => $user->getId(),
                'name' => $user->getEmail(),
                'account' => $user->getAccount(),
            ];
    
            return $this->json(null, 204);
        }
    }




}