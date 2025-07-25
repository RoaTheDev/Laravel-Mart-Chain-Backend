<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','show']]);
    }

    /**
     * @OA\Get(
     *     path="/products",
     *     tags={"Product"},
     *     summary="Get list of products",
     *     description="Retrieve a paginated list of all products, optionally filtered by search term",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term to filter products by name or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=15)
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'products' => $products->items(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     tags={"Product"},
     *     summary="Get a specific product",
     *     description="Retrieve a single product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Product"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Product not found"),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'error' => 'Product not found',
                'status_code' => 404
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $product,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     tags={"Product"},
     *     summary="Create a new product",
     *     description="Create a new product with name, cost, price, image, description, and category",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "cost", "price", "category_id"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Laptop"),
     *             @OA\Property(property="cost", type="number", format="float", example=500.00),
     *             @OA\Property(property="price", type="number", format="float", example=699.99),
     *             @OA\Property(property="image", type="string", nullable=true, example="https://example.com/images/laptop.jpg"),
     *             @OA\Property(property="description", type="string", nullable=true, example="High-performance laptop"),
     *             @OA\Property(property="category_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Product"),
     *             @OA\Property(property="status_code", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:category,id',
        ]);

        if ($validator->fails()) {
            $flatErrors = collect($validator->errors()->messages())
                ->mapWithKeys(fn($messages, $field) => [$field => $messages[0]])
                ->toArray();

            return response()->json([
                'status' => 'error',
                'errors' => $flatErrors,
                'status_code' => 422
            ], 422);
        }

        $product = Product::create($request->only('name', 'cost', 'price', 'image', 'description', 'category_id'));

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'data' => $product,
            'status_code' => 201
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     tags={"Product"},
     *     summary="Update a product",
     *     description="Update an existing product by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "cost", "price", "category_id"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Laptop"),
     *             @OA\Property(property="cost", type="number", format="float", example=500.00),
     *             @OA\Property(property="price", type="number", format="float", example=699.99),
     *             @OA\Property(property="image", type="string", nullable=true, example="https://example.com/images/laptop.jpg"),
     *             @OA\Property(property="description", type="string", nullable=true, example="High-performance laptop"),
     *             @OA\Property(property="category_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Product"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Product not found"),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'error' => 'Product not found',
                'status_code' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:category,id',
        ]);

        if ($validator->fails()) {
            $flatErrors = collect($validator->errors()->messages())
                ->mapWithKeys(fn($messages, $field) => [$field => $messages[0]])
                ->toArray();

            return response()->json([
                'status' => 'error',
                'errors' => $flatErrors,
                'status_code' => 422
            ], 422);
        }

        $product->update($request->only('name', 'cost', 'price', 'image', 'description', 'category_id'));

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'data' => $product,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     tags={"Product"},
     *     summary="Delete a product",
     *     description="Soft delete a product by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Product deleted successfully"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Product not found"),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     )
     * )
     */
    public function delete($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'error' => 'Product not found',
                'status_code' => 404
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/products/restore",
     *     tags={"Product"},
     *     summary="Restore a soft-deleted product",
     *     description="Restore a previously soft-deleted product by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Product restored successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Product"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Product not found"),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     )
     * )
     */
    public function restore(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $flatErrors = collect($validator->errors()->messages())
                ->mapWithKeys(fn($messages, $field) => [$field => $messages[0]])
                ->toArray();

            return response()->json([
                'status' => 'error',
                'errors' => $flatErrors,
                'status_code' => 422
            ], 422);
        }

        $product = Product::onlyTrashed()->find($request->id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'error' => 'Product not found',
                'status_code' => 404
            ], 404);
        }

        $product->restore();

        return response()->json([
            'status' => 'success',
            'message' => 'Product restored successfully',
            'data' => $product,
            'status_code' => 200
        ]);
    }
}
