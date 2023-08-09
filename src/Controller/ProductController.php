<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function listProducts(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, PaginatorInterface $paginator, SerializerInterface $serialize): JsonResponse
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

        $context = SerializationContext::create();
        $jsonProducts = $serialize->serialize($products, 'json', $context);
        return new JsonResponse($jsonProducts, Response::HTTP_OK, ['accept' => 'json'], true);
        //return $this->json($products, 200, [], ["groups" => ["extended"]]);

    }

    #[Route('/api/products/{id}', name: 'api_product_details', methods: ['GET'])]
    public function getProductDetails(Product $product, SerializerInterface $serialize): JsonResponse
    {
        //     $response = [
//         'id' => $product->getId(),
//         'name' => $product->getName(),
//         'price' => $product->getPrice(),
//         'description' => $product->getDescription(),
//         'sku' => $product->getSku(),
//         'available' => $product->isAvailable(),
//         'createdAt' => $product->getCreatedAt(),
//         'updatedAt' => $product->getUpdatedAt(),
//     ];

        $context = SerializationContext::create();
        $jsonProduct = $serialize->serialize($product, 'json', $context);
        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ['accept' => 'json'], true);
        //          return $this->json($response);
    }

    #[Route('/api/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(EntityManagerInterface $entityManager, Product $product): JsonResponse
    {
        try {
            $product->setDeleted(true);
            $entityManager->flush();

            return $this->json(null, 204);
        } catch (\Exception $e) {
            return $this->json(['message' => "Something is wrong with your properties"], 404);
        }
    }

    #[Route('/api/products', name: 'api_create_product', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serialize): JsonResponse
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

        $context = SerializationContext::create();
        $jsonProduct = $serialize->serialize($product, 'json', $context);
        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ['accept' => 'json'], true);
      //  return $this->json($product, 201, []);

    }
}