<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     path="/staff",
     *     tags={"Staff"},
     *     summary="Get list of staff",
     *     description="Retrieve a paginated list of all staff, optionally filtered by search term",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term to filter staff by name, gender, pob, address, phone, or nation_id_card",
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
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Staff")),
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
        $query = Staff::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('gender', 'like', "%$search%")
                    ->orWhere('pob', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('nation_id_card', 'like', "%$search%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $staff = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $staff,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Get(
     *     path="/staff/{id}",
     *     tags={"Staff"},
     *     summary="Get a specific staff member",
     *     description="Retrieve a single staff member by ID",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Staff"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Staff not found"),
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
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json([
                'status' => 'error',
                'error' => 'Staff not found',
                'status_code' => 404
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $staff,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/staff",
     *     tags={"Staff"},
     *     summary="Create a new staff member",
     *     description="Create a new staff member with position, name, gender, date of birth, place of birth, address, phone, and national ID card",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"position_id", "name", "gender", "dob", "pob", "address", "phone", "nation_id_card"},
     *             @OA\Property(property="position_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", maxLength=255, example="John Doe"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="pob", type="string", maxLength=255, example="New York"),
     *             @OA\Property(property="address", type="string", maxLength=255, example="123 Main St"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="+1234567890"),
     *             @OA\Property(property="nation_id_card", type="string", maxLength=50, example="123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Staff created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Staff created successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Staff"),
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
            'position_id' => 'required|integer|exists:position,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date',
            'pob' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'nation_id_card' => 'required|string|max:50',
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

        $staff = Staff::create($request->only('position_id', 'name', 'gender', 'dob', 'pob', 'address', 'phone', 'nation_id_card'));

        return response()->json([
            'status' => 'success',
            'message' => 'Staff created successfully',
            'data' => $staff,
            'status_code' => 201
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/staff/{id}",
     *     tags={"Staff"},
     *     summary="Update a staff member",
     *     description="Update an existing staff member by ID",
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
     *             required={"position_id", "name", "gender", "dob", "pob", "address", "phone", "nation_id_card"},
     *             @OA\Property(property="position_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", maxLength=255, example="John Doe"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="pob", type="string", maxLength=255, example="New York"),
     *             @OA\Property(property="address", type="string", maxLength=255, example="123 Main St"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="+1234567890"),
     *             @OA\Property(property="nation_id_card", type="string", maxLength=50, example="123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Staff updated successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Staff"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Staff not found"),
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
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json([
                'status' => 'error',
                'error' => 'Staff not found',
                'status_code' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'position_id' => 'required|integer|exists:position,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'dob' => 'required|date',
            'pob' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'nation_id_card' => 'required|string|max:50',
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

        $staff->update($request->only('position_id', 'name', 'gender', 'dob', 'pob', 'address', 'phone', 'nation_id_card'));

        return response()->json([
            'status' => 'success',
            'message' => 'Staff updated successfully',
            'data' => $staff,
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/staff/{id}",
     *     tags={"Staff"},
     *     summary="Delete a staff member",
     *     description="Soft delete a staff member by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Staff deleted successfully"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Staff not found"),
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
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json([
                'status' => 'error',
                'error' => 'Staff not found',
                'status_code' => 404
            ], 404);
        }

        $staff->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Staff deleted successfully',
            'status_code' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/staff/restore",
     *     tags={"Staff"},
     *     summary="Restore a soft-deleted staff member",
     *     description="Restore a previously soft-deleted staff member by ID",
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
     *         description="Staff restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Staff restored successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Staff"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Staff not found"),
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

        $staff = Staff::onlyTrashed()->find($request->id);

        if (!$staff) {
            return response()->json([
                'status' => 'error',
                'error' => 'Staff not found',
                'status_code' => 404
            ], 404);
        }

        $staff->restore();

        return response()->json([
            'status' => 'success',
            'message' => 'Staff restored successfully',
            'data' => $staff,
            'status_code' => 200
        ]);
    }
}
