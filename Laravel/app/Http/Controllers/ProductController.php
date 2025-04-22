<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ProductController extends Controller
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
     * @return mixed
     */
    public function getProducts(): mixed
    {
        return response()->json(self::PRODUCTS, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getProductItem(string $id): mixed
    {
        $product = $this->getProductItemById(self::PRODUCTS, $id);

        if (!$product) {
            return response()->json(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $product], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function create(Request $request): mixed
    {
        $requestData = json_decode($request->getContent(), true);

        $productId = random_int(1, 100);

        $newProductData = [
            'id'          => $productId,
            'name'        => $requestData['name'],
            'description' => $requestData['description'],
            'price'       => $requestData['price']
        ];

        // TODO insert to db

        return response()->json([
            'data' => $newProductData
        ], Response::HTTP_CREATED);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function edit(string $id): mixed
    {
        $product = $this->getProductItemById(self::PRODUCTS, $id);

        if (!$product) {
            return response()->json(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $product], Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function destroy(string $id): mixed
    {
        $product = $this->getProductItemById(self::PRODUCTS, $id);

        if (!$product) {
            return response()->json(['data' => ['error' => 'Not found product by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        // TODO remove from db

        return response()->json([], Response::HTTP_NO_CONTENT);
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
