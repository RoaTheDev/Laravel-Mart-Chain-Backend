<?php

namespace App\Http\Controllers;

use App\Helpers\validation\validation;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="FakeStore API Documentation",
 *      description="Swagger documentation for FakeStore API"
 * )
 */
class BranchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/branch/lists",
     *     tags={"Branch"},
     *     summary="Get list of branch",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    function lists(Request $request)
    {
        $data = Branch::all();
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'status_code' => 200
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/branch/create",
     *     summary="Create a new branch",
     *     tags={"Branch"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "location", "contact_number"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Phnom Penh Branch"),
     *             @OA\Property(property="location", type="string", maxLength=255, example="Phnom Penh"),
     *             @OA\Property(property="contact_number", type="string", maxLength=20, example="012345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);
        $flatErrors = collect($validator->errors()->messages())->mapWithKeys(function ($messages, $field) {
            return [$field => $messages[0]];
        })->toArray();
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $flatErrors,
                'status_code' => 422
            ], 422);
        }
        //add cmm
        $branch = new Branch();
        $branch->name = $request->name;
        $branch->location = $request->location;
        $branch->contact_number = $request->contact_number;
        $branch->save();
        return response()->json([
            'status' => 'success',
            'new_data' => $branch,
            'status_code' => 200
        ]);
    }
    function update(Request $request)
    {
        $branch = Branch::find($request->id);
        if ($branch != null) {
            $branch->name = $request->name;
            $branch->location = $request->location;
            $branch->contact_number = $request->contact_number;
            $branch->save();
        }
        return response()->json([
            'status' => 'success',
            'updated_data' => $branch,
            'status_code' => 200
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/branch/delete",
     *     summary="Delete a branch by ID",
     *     tags={"Branch"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Branch deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     )
     * )
     */
    function delete(Request $request)
    {
        $branch = Branch::find($request->id);
        if ($branch != null) {
            $branch->delete();
            return response()->json([
                'status' => 'success',
                'deleted_data' => $branch,
                'status_code' => 200
            ]);
        } else {
            return response()->json([
                'status' => 'resource not found 🥹',
                'status_code' => 200
            ]);
        }
    }
}
