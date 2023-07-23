<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 30; $i++) {
            $user = new User();
            $user->setEmail('dussennem2000' . $i . '@yahoo.fr');
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'testpw'));
            $account = $this->getReference('account1');
            $user->setAccount($account);
            $manager->persist($user);
            $user->setRoles(['ROLE_USER']);
        }

        $manager->flush();
    }
}