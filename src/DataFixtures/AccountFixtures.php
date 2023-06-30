<?php

namespace App\DataFixtures;

use App\Entity\Account;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AccountFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $account = new Account();
        $account->setEmail('dussennem2000@yahoo.fr');
        $account->setName('client1');
        $account->setCompany('test');
        $this->addReference('account1', $account);
        $manager->persist($account);

        $manager->flush();
    }
}
