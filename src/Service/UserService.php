<?php

namespace App\Service;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $userPasswordHasher,
        private FormFactoryInterface $formFactory
    ) {}

    /**
     * @param mixed $account
     * @return User[]|array
     */
    public function showUsersForAccount(mixed $account): array
    {
        return $this->userRepository->findBy(['account' => $account]);
    }

    /**
     * @param array $parameters
     * @return array
     */
    public function registerUser(array $parameters): array
    {
        $user = new User();
        $form = $this->formFactory->create(UserType::class, $user);
        $form->submit($parameters);

        /** @var ConstraintViolationListInterface $errors */
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return ['errors' => $errorMessages];
        }

        if ($form->isValid()) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $form->get('password')->getData()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return ['user' => $user];
        }

        return ['errors' => 'Invalid form submission'];
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
