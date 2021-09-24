<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Validator;
use Response;

class authApiController extends Controller
{
    public function login(){
        return response()->json(['status'=>'failed']);
    }

    public function checkLogin(Request $request){

        $validator = Validator::make(request()->all(), [
            'employeeNumber' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $credentials = $request->only('employeeNumber', 'password');

        if(!auth()->validate($credentials)){
            return abort(401);
        }
        else{
            $user = Employee::where('employeeNumber', $credentials['employeeNumber'])->first();
            $user->tokens()->delete();
            $token = $user->createToken('postman');
            return response()->json(['token' => $token->plainTextToken]);
        }
    }
}
