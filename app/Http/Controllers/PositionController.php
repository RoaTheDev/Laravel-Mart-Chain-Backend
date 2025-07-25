<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    /**
     * @OA\Get(
     *     path="/positions",
     *     tags={"Position"},
     *     summary="Get list of positions",
     *     description="Retrieve a paginated list of all positions, optionally filtered by search term",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term to filter positions by name or description",
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
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Position")),
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
        $query = Position::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 15);
        $positions = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'positions' => $positions->items(),
                'current_page' => $positions->currentPage(),
                'per_page' => $positions->perPage(),
                'total' => $positions->total(),
            ],
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Get(
     *     path="/positions/{id}",
     *     tags={"Position"},
     *     summary="Get a specific position",
     *     description="Retrieve a single position by ID",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Position"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Position not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Position not found"),
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
        $position = Position::find($id);

        if (!$position) {
            return response()->json([
                'status' => 'error',
                'error' => 'Position not found',
                'status_code' => 404
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $position,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/positions",
     *     tags={"Position"},
     *     summary="Create a new position",
     *     description="Create a new position with branch, name, and description",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"branch_id", "name"},
     *             @OA\Property(property="branch_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", maxLength=255, example="Manager"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Branch manager role")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Position created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Position created successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Position"),
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
            'branch_id' => 'required|integer|exists:branch,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
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

        $position = Position::create($request->only('branch_id', 'name', 'description'));

        return response()->json([
            'status' => 'success',
            'message' => 'Position created successfully',
            'data' => $position,
            'status_code' => 201
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/positions/{id}",
     *     tags={"Position"},
     *     summary="Update a position",
     *     description="Update an existing position by ID",
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
     *             required={"branch_id", "name"},
     *             @OA\Property(property="branch_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", maxLength=255, example="Manager"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Branch manager role")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Position updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Position updated successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Position"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Position not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Position not found"),
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
        $position = Position::find($id);

        if (!$position) {
            return response()->json([
                'status' => 'error',
                'error' => 'Position not found',
                'status_code' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|integer|exists:branch,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
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

        $position->update($request->only('branch_id', 'name', 'description'));

        return response()->json([
            'status' => 'success',
            'message' => 'Position updated successfully',
            'data' => $position,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/positions/{id}",
     *     tags={"Position"},
     *     summary="Delete a position",
     *     description="Soft delete a position by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Position deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Position deleted successfully"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Position not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Position not found"),
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
        $position = Position::find($id);

        if (!$position) {
            return response()->json([
                'status' => 'error',
                'error' => 'Position not found',
                'status_code' => 404
            ], 404);
        }

        $position->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Position deleted successfully',
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/positions/restore",
     *     tags={"Position"},
     *     summary="Restore a soft-deleted position",
     *     description="Restore a previously soft-deleted position by ID",
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
     *         description="Position restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Position restored successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Position"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Position not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Position not found"),
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

        $position = Position::onlyTrashed()->find($request->id);

        if (!$position) {
            return response()->json([
                'status' => 'error',
                'error' => 'Position not found',
                'status_code' => 404
            ], 404);
        }

        $position->restore();

        return response()->json([
            'status' => 'success',
            'message' => 'Position restored successfully',
            'data' => $position,
            'status_code' => 200
        ]);
    }
}
