<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('samsungv2');
        $product->setPrice('500');
        $product->setDescription('lorem lorem loren');
        $product->setSku('samsungv2');
        $product->setAvailable('1');

        $manager->persist($product);

        $manager->flush();
    }
}