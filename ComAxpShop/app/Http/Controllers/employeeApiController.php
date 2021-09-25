<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Employee;
use Validator;


class employeeApiController extends Controller
{
    public function createEmployee(Request $request){
        $validator = Validator::make(request()->all(), [
            'lastName' => 'required',
            'firstName' => 'required',
            'extension' => 'required',
            'email' => 'required|email|unique:employees',
            'officeCode' => 'required|exists:offices,officeCode',
            'reportsTo' => 'required|exists:employees,employeeNumber',
            'jobTitle' => 'required',
            'password' => 'required|min:6',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $employee = $request->all();

        $fetch = $this->addToDB($employee);
    
        return response()->json(['message' => 'success']);
    }

    public function addToDB($employeeData){
        return Employee::create([
            'lastName' => $employeeData['lastName'],
            'firstName' => $employeeData['firstName'],
            'extension' => $employeeData['extension'],
            'email' => $employeeData['email'],
            'officeCode' => $employeeData['officeCode'],
            'reportsTo' => $employeeData['reportsTo'],
            'jobTitle' => $employeeData['jobTitle'],
            'password' => bcrypt($employeeData['password'])
          ]);
    }
}
