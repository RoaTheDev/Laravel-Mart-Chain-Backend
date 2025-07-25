<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     path="/invoice-items",
     *     tags={"InvoiceItem"},
     *     summary="Get list of invoice items",
     *     description="Retrieve a paginated list of all invoice items, optionally filtered by invoice_id or product_id",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="invoice_id",
     *         in="query",
     *         description="Filter invoice items by invoice ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="Filter invoice items by product ID",
     *         required=false,
     *         @OA\Schema(type="integer")
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
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/InvoiceItem")),
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
        $query = InvoiceItem::query();

        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->input('invoice_id'));
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        $perPage = $request->input('per_page', 15);
        $invoiceItems = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'invoice_items' => $invoiceItems->items(),
                'current_page' => $invoiceItems->currentPage(),
                'per_page' => $invoiceItems->perPage(),
                'total' => $invoiceItems->total(),
            ],
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Get(
     *     path="/invoice-items/{id}",
     *     tags={"InvoiceItem"},
     *     summary="Get a specific invoice item",
     *     description="Retrieve a single invoice item by ID",
     *     security={{"bearerAuth": {}}},
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/InvoiceItem"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Invoice item not found"),
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
        $invoiceItem = InvoiceItem::find($id);

        if (!$invoiceItem) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invoice item not found',
                'status_code' => 404
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $invoiceItem,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/invoice-items",
     *     tags={"InvoiceItem"},
     *     summary="Create a new invoice item",
     *     description="Create a new invoice item with invoice ID, product ID, quantity, and price",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"invoice_id", "product_id", "qty", "price"},
     *             @OA\Property(property="invoice_id", type="integer", example=1),
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="qty", type="integer", example=2),
     *             @OA\Property(property="price", type="number", format="float", example=499.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Invoice item created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice item created successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/InvoiceItem"),
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
            'invoice_id' => 'required|integer|exists:invoice,id',
            'product_id' => 'required|integer|exists:product,id',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
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

        $invoiceItem = InvoiceItem::create($request->only('invoice_id', 'product_id', 'qty', 'price'));

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice item created successfully',
            'data' => $invoiceItem,
            'status_code' => 201
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/invoice-items/{id}",
     *     tags={"InvoiceItem"},
     *     summary="Update an invoice item",
     *     description="Update an existing invoice item by ID",
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
     *             required={"invoice_id", "product_id", "qty", "price"},
     *             @OA\Property(property="invoice_id", type="integer", example=1),
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="qty", type="integer", example=2),
     *             @OA\Property(property="price", type="number", format="float", example=499.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice item updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice item updated successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/InvoiceItem"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Invoice item not found"),
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
        $invoiceItem = InvoiceItem::find($id);

        if (!$invoiceItem) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invoice item not found',
                'status_code' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|integer|exists:invoice,id',
            'product_id' => 'required|integer|exists:product,id',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
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

        $invoiceItem->update($request->only('invoice_id', 'product_id', 'qty', 'price'));

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice item updated successfully',
            'data' => $invoiceItem,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/invoice-items/{id}",
     *     tags={"InvoiceItem"},
     *     summary="Delete an invoice item",
     *     description="Soft delete an invoice item by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice item deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice item deleted successfully"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Invoice item not found"),
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
        $invoiceItem = InvoiceItem::find($id);

        if (!$invoiceItem) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invoice item not found',
                'status_code' => 404
            ], 404);
        }

        $invoiceItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice item deleted successfully',
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/invoice-items/restore",
     *     tags={"InvoiceItem"},
     *     summary="Restore a soft-deleted invoice item",
     *     description="Restore a previously soft-deleted invoice item by ID",
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
     *         description="Invoice item restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice item restored successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/InvoiceItem"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Invoice item not found"),
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

        $invoiceItem = InvoiceItem::onlyTrashed()->find($request->id);

        if (!$invoiceItem) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invoice item not found',
                'status_code' => 404
            ], 404);
        }

        $invoiceItem->restore();

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice item restored successfully',
            'data' => $invoiceItem,
            'status_code' => 200
        ]);
    }
}
