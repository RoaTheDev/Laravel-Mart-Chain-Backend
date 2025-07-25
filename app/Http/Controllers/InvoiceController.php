<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     path="/invoices",
     *     tags={"Invoice"},
     *     summary="Get list of invoices",
     *     description="Retrieve a paginated list of all invoices, optionally filtered by user_id",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter invoices by user ID",
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
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Invoice")),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=15)
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
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
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $perPage = $request->input('per_page', 15);
        $invoices = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'invoices' => $invoices->items(),
                'current_page' => $invoices->currentPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
            ],
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Get(
     *     path="/invoices/{id}",
     *     tags={"Invoice"},
     *     summary="Get a specific invoice",
     *     description="Retrieve a single invoice by ID",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Invoice"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Invoice not found"),
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
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invoice not found',
                'status_code' => 404
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $invoice,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/invoices",
     *     tags={"Invoice"},
     *     summary="Create a new invoice",
     *     description="Create a new invoice with user ID and total",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "total"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="total", type="number", format="float", example=999.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Invoice created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice created successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Invoice"),
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
            'user_id' => 'required|integer|exists:users,id',
            'total' => 'required|numeric|min:0',
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

        $invoice = Invoice::create($request->only('user_id', 'total'));

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice created successfully',
            'data' => $invoice,
            'status_code' => 201
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/invoices/{id}",
     *     tags={"Invoice"},
     *     summary="Update an invoice",
     *     description="Update an existing invoice by ID",
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
     *             required={"user_id", "total"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="total", type="number", format="float", example=999.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice updated successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Invoice"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Invoice not found"),
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
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invoice not found',
                'status_code' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'total' => 'required|numeric|min:0',
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

        $invoice->update($request->only('user_id', 'total'));

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice updated successfully',
            'data' => $invoice,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/invoices/{id}",
     *     tags={"Invoice"},
     *     summary="Delete an invoice",
     *     description="Soft delete an invoice by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice deleted successfully"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Invoice not found"),
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
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invoice not found',
                'status_code' => 404
            ], 404);
        }

        $invoice->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice deleted successfully',
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/invoices/restore",
     *     tags={"Invoice"},
     *     summary="Restore a soft-deleted invoice",
     *     description="Restore a previously soft-deleted invoice by ID",
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
     *         description="Invoice restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Invoice restored successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Invoice"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Invoice not found"),
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

        $invoice = Invoice::onlyTrashed()->find($request->id);

        if (!$invoice) {
            return response()->json([
                'status' => 'error',
                'error' => 'Invoice not found',
                'status_code' => 404
            ], 404);
        }

        $invoice->restore();

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice restored successfully',
            'data' => $invoice,
            'status_code' => 200
        ]);
    }
}
