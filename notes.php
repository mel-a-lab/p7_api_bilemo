<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function getProducts(): JsonResponse
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        $response = [];
        foreach ($products as $product) {
            $response[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
            ];
        }


        return $this->json($response);
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
}