<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Employee;
use Validator;


class employeeApiController extends Controller
{
    public function getEmployee(){
        $employees = Employee::query()
                        ->orderBy('employeeNumber', 'asc')
                        ->get();

        foreach($employees as $employee) {
            $employee->makeHidden(['password']);
        }

        return $employees;
    }

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
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $employee = $request->all();

        $fetch = $this->addToDB($employee);
    
        return response()->json(['message' => 'add employee success']);
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

    public function deleteEmployee(Request $request){
        $validator = Validator::make(request()->all(), [
            'employeeNumber' => 'required|exists:employees,employeeNumber',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $input = $request->only('employeeNumber');
        $targetEmployee = Employee::where('employeeNumber', $input)->first();
        $targetEmployee->delete();

        return response()->json(['message' => 'delete employee success']);
    }

    public function updateEmployee(Request $request){
        $validator = Validator::make(request()->all(), [
            'employeeNumber' => 'required|exists:employees,employeeNumber',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $input = $request->all();

        $targetEmployee = Employee::find($input['employeeNumber']);
        $targetEmployee->fill($input)->update();

        return response('Data updated');
    }
}
