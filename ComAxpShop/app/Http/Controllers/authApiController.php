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
            return response()->json([
                'errors' => $validator->errors()], 401);
        }

        $credentials = $request->only('employeeNumber', 'password');

        if(!auth()->validate($credentials)){
            return response()->json(['message' => 'wrong username or password'], 401);
        }
        else{
            $user = Employee::where('employeeNumber', $credentials['employeeNumber'])->first();
            $user->tokens()->delete();
            $jobTitle = $user['jobTitle'];
            
            $userFirstName = $user['firstName'];
            $userLastName = $user['lastName'];
            $userFullName = $userFirstName.' '.$userLastName;

            if(str_contains($jobTitle, 'Sales Manager')){
                $token = $user->createToken($userFullName, ['Employee', 'HR', 'Salesman']);
                return response()->json(['Token' => $token->plainTextToken]);
            }

            switch($jobTitle){
                case 'President': $token = $user->createToken($userFullName, ['Employee', 'President']); break;
                case 'VP Sales' : $token = $user->createToken($userFullName, ['Employee', 'Salesman', 'VP']); break;
                case 'VP Manager' : $token = $user->createToken($userFullName, ['Employee', 'VP']); break;
                case 'Sales Rep' : $token = $user->createToken($userFullName, ['Employee', 'Salesman']); break;
            }

            return response()->json(['Token' => $token->plainTextToken]);
        }
    }

    public function dashboardType(){
        $user = auth()->user();

        if($user->tokenCan('President')){
            Route::get('/employees', [employeeApiController::class, 'getEmployee'])->name('employee');
            // return response(['President']);
        }
        else if($user->tokenCan('VP Marketing')){
            // Route::get('');
            return response(['VP Marketing']);
        }
        else if($user->tokenCan('VP Sales')){
            // Route::get('');
            return response(['VP Sales']);
        }
        else if($user->tokenCan('Sales Manager (APAC)')){
            // Route::get('');
            return response(['Sales Manager (APAC)']);
        }
        else if($user->tokenCan('Sales Manager (NA)')){
            // Route::get('');
            return response(['Sales Manager (NA)']);
        }
        else if($user->tokenCan('Sales Rep')){
            // Route::get('');
            return response(['Sales Rep']);
        }
        else{
            return response(['janitor']);
        }
    }
}
