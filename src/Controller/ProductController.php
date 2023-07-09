<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function listProducts(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $page = $request->query->getInt('page', 1); // Récupère le numéro de page depuis la requête
        $limit = $request->query->getInt('limit', 10); // Récupère le nombre d'éléments par page depuis la requête

        $pagination = $paginator->paginate(
            $productRepository->findAll(),
            // Query pour récupérer tous les produits
            $page,
            // Numéro de page
            $limit // Nombre d'éléments par page
        );

        $products = $pagination->getItems(); // Récupère les produits de la page courante

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

        return $this->json($product, 201, []);
    }


}