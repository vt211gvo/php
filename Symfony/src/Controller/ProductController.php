<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
final class ProductController extends AbstractController
{

    public const PRODUCTS = [
        [
            'id'          => '4263ed5c-8d78-4b65-99d3-059321ca5629',
            'name'        => 'product1',
            'description' => 'description1',
            'price'       => '100'
        ],
        [
            'id'          => '9897dc23-e6e6-47f5-bc20-daa776256ece',
            'name'        => 'product2',
            'description' => 'description2',
            'price'       => '200'
        ],
        [
            'id'          => '3992b376-1867-4076-94e6-cd7612bb690a',
            'name'        => 'product3',
            'description' => 'description3',
            'price'       => '300'
        ]
    ];

    /**
     * @return JsonResponse
     */
    #[Route('/products', name: 'get_products', methods: [Request::METHOD_GET])]
    public function getProducts(): JsonResponse
    {
        return new JsonResponse(['data' => self::PRODUCTS], Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/products/{id}', name: 'get_product_item', methods: [Request::METHOD_GET])]
    public function getProductItem(string $id): JsonResponse
    {
        $product = $this->getProductItemById(self::PRODUCTS, $id);

        if (!$product) {
            return new JsonResponse(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['data' => $product], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/products', name: 'post_products', methods: [Request::METHOD_POST])]
    public function createProduct(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $productId = mt_rand(1, 100);

        $newProductData = [
            'id'          => $productId,
            'name'        => $requestData['name'],
            'description' => $requestData['description'],
            'price'       => $requestData['price']
        ];

        // TODO insert to db

        return new JsonResponse([
            'data' => $newProductData
        ], Response::HTTP_CREATED);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/products/{id}', name: 'patch_products', methods: [Request::METHOD_PATCH])]
    public function updateProduct(string $id, Request $request): JsonResponse
    {
        $product = $this->getProductItemById(self::PRODUCTS, $id);

        if (!$product) {
            return new JsonResponse(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        $requestData = json_decode($request->getContent(), true);

        $product['name'] = $requestData['name'];
        $product['description'] = $requestData['description'];
        $product['price'] = $requestData['price'];

        // TODO insert to db

        return new JsonResponse([
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/products/{id}', name: 'delete_products', methods: [Request::METHOD_DELETE])]
    public function deleteProduct(string $id): JsonResponse
    {
        $product = $this->getProductItemById(self::PRODUCTS, $id);

        if (!$product) {
            return new JsonResponse(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        // TODO remove from db

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param array $products
     * @param string $id
     * @return array|null
     */
    public function getProductItemById(array $products, string $id): ?array
    {
        foreach ($products as $product) {
            if ($product['id'] != $id) {
                continue;
            }

            return $product;
        }

        return null;
    }

}