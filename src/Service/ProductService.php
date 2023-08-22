<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductService
{
    private $productRepository;
    private $entityManager;
    private $validator;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function listAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function createProduct(array $data): array
    {
        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description']);
        $product->setSku($data['sku']);
        $product->setAvailable($data['available']);
        $product->setCreatedAt(new \DateTime('now'));
        $product->setUpdatedAt(new \DateTime('now'));

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return ['errors' => $errorMessages];
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return ['product' => $product];
    }

    public function deleteProduct(Product $product): void
    {
        $product->setDeleted(true);
        $this->entityManager->flush();
    }
}
