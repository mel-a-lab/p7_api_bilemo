<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/api/list/products', name: 'api_products', methods: ['GET'])]
    public function listProducts(ProductRepository $productRepository): JsonResponse
    {

        $products = $productRepository->findAll();
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
}
