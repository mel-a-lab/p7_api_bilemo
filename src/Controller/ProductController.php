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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;


class ProductController extends AbstractController
{


    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function listProducts(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, PaginatorInterface $paginator, SerializerInterface $serialize): JsonResponse
    {

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $pagination = $paginator->paginate(
            $productRepository->findAll(),
            $page,
            $limit
        );

        $products = $pagination->getItems();

 //       $context = SerializationContext::create();
        $jsonProducts = $this->serializer->serialize($products, 'json');
        return new JsonResponse($jsonProducts, Response::HTTP_OK, ['accept' => 'json'], true);

    }

    #[Route('/api/products/{id}', name: 'api_product_details', methods: ['GET'])]
    public function getProductDetails(Product $product, SerializerInterface $serialize): JsonResponse
    {
        $context = SerializationContext::create();
        $jsonProduct = $serialize->serialize($product, 'json', $context);
        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ['accept' => 'json'], true);
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
    public function createProduct(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serialize, ValidatorInterface $validator): JsonResponse
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

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($product);
        $entityManager->flush();

        $context = SerializationContext::create();
        $jsonProduct = $serialize->serialize($product, 'json', $context);
        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, ['accept' => 'json'], true);

    }
}