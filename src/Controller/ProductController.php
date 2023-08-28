<?php

namespace App\Controller;

use App\Service\ProductService;
use App\Entity\Product;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;



class ProductController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function listProducts(ProductService $productService, Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $products = $productService->listAllProducts();

        $pagination = $paginator->paginate($products, $page, $limit);

        $jsonProducts = $this->serializer->serialize($pagination->getItems(), 'json');
        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

    #[Route('/api/products/{id}', name: 'api_product_details', methods: ['GET'])]
    public function getProductDetails(Product $product): JsonResponse
    {
        $context = SerializationContext::create();
        $jsonProduct = $this->serializer->serialize($product, 'json', $context);
        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }

    #[Route('/api/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(Product $product, ProductService $productService): JsonResponse
    {
        try {
            $productService->deleteProduct($product);
            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->json(['message' => "Something went wrong"], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Cette méthode permet d'ajouter un produit lié au client.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne le produit créé",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class, groups={"getProducts"}))
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Mauvaise requête de l'utilisateur"
     * )
     *
     * @OA\Response(
     *     response=403,
     *     description="Droits insuffisants pour ajouter un produit"
     * )
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         example={
     *              "name": "samsungv2",
     *              "price": "22",
     *              "description": "ssskkbbhome",
     *              "sku": "iphdssxone13",
     *              "available": "oui"
     *         },
     *         @OA\Schema (
     *              type="object",
     *              @OA\Property(property="status", required=true, description="Event Status", type="string"),
     *              @OA\Property(property="comment", required=false, description="Change Status Comment", type="string")
     *         )
     *     )
     * )
     *
     * @OA\Tag(name="Produits")
     */
    #[Route('/api/products', name: 'api_create_product', methods: ['POST'])]
    public function createProduct(Request $request, ProductService $productService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $result = $productService->createProduct($data);

        if (array_key_exists('errors', $result)) {
            return $this->json(['errors' => $result['errors']], Response::HTTP_BAD_REQUEST);
        }

        $product = $result['product'];
        $context = SerializationContext::create();
        $jsonProduct = $this->serializer->serialize($product, 'json', $context);
        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, [], true);
    }
}
