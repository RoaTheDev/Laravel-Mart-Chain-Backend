<?php

namespace App\Http\Controllers;

use App\Helpers\validation\validation;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    //
    function lists(Request $request){
        $data = Branch::all();
        return response()->json([
            'status'=> 'success',
            'data'=>$data,
            'status_code'=>200
        ]);
    }

    function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'location'       => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);
        $flatErrors = collect($validator->errors()->messages())->mapWithKeys(function($messages, $field) {
            return [$field => $messages[0]];
        })->toArray();
        if ($validator->fails()) {
            return response()->json([
                'status'      => 'error',
                'errors'      => $flatErrors,
                'status_code' => 422
            ], 422);
        }

        $branch = new Branch();
        $branch->name = $request->name;
        $branch->location = $request->location;
        $branch->contact_number = $request->contact_number;
        $branch->save();
        return response()->json([
            'status'=> 'success',
            'new_data'=>$branch,
            'status_code'=>200
        ]);
    }

    function update(Request $request){
        $branch = Branch::find($request->id);
        if ($branch != null){
            $branch->name = $request->name;
            $branch->location = $request->location;
            $branch->contact_number = $request->contact_number;
            $branch->save();
        }
        return response()->json([
            'status'=> 'success',
            'updated_data'=>$branch,
            'status_code'=>200
        ]);
    }

    function delete(Request $request){
        $branch = Branch::find($request->id);
        if ($branch != null){
            $branch->delete();
            return response()->json([
                'status'=> 'success',
                'deleted_data'=>$branch,
                'status_code'=>200
            ]);
        }else{
            return response()->json([
                'status'=> 'resource not found 🥹',
                'status_code'=>200
            ]);
        }
    }
}
