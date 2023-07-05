<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/api/list/products', name: 'api_products', methods: ['GET'])]
    public function listProducts(ProductRepository $productRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllProducts-" . $page . "-" . $limit;
        $products = $cachePool->get($idCache, function (ItemInterface $item) use ($productRepository, $page, $limit) {
            $item->tag("productsCache");

        $products = $productRepository->findAllWithPagination($page, $limit);    });

        $jsonBookList = $serializer->serialize($products, 'json', ['groups' => 'getBooks']);
        return $this->json($products, 200, [], ["groups" => ["extended"]]);
    }

    #[Route('/api/products/{id}', name: 'api_product_details', methods: ['GET'])]
    public function getProductDetails(Product $product): JsonResponse
    {
        $response = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'sku' => $product->getSku(),
            'available' => $product->isAvailable(),
            'createdAt' => $product->getCreatedAt(),
            'updatedAt' => $product->getUpdatedAt(),
        ];

        return $this->json($response);
    }

    #[Route('/api/products/{product}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(EntityManagerInterface $entityManager, Product $product): JsonResponse
    {
        $product->setDeleted(true);
        $entityManager->flush();

        return $this->json(null, 204);
    }

    #[Route('/api/products', name: 'api_create_product', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description']);
        $product->setSku($data['sku']);
        $product->setAvailable($data['available']);
        $product->setCreatedAt(new \DateTime('now'));
        $product->setUpdatedAt(new \DateTime('now'));

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json($product, 201, [], ["groups" => ["extended"]]);
    }


}