<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Employee;
use Validator;


class employeeApiController extends Controller
{
    public function getEmployee(){
        $user = auth()->user();
        if($user->tokenCan('Employee')){        // all employee can access this section
            $employees = Employee::query()
                        ->orderBy('employeeNumber', 'asc')
                        ->get();

            foreach($employees as $employee) {
                $employee->makeHidden(['password']);
            }

            return $employees;
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function createEmployee(Request $request){
        $user = auth()->user();

        // if($user->tokenCan('HR')){    real
        if($user->tokenCan('Employee')){
            $validator = Validator::make(request()->all(), [
                'lastName' => 'required',
                'firstName' => 'required',
                'extension' => 'required',
                'email' => 'required|email|unique:employees',
                'officeCode' => 'required|exists:offices,officeCode',
                'reportsTo' => 'nullable|exists:employees,employeeNumber',
                'jobTitle' => 'required',
                'password' => 'required|min:6',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 400);
            }
    
            $employee = $request->all();
    
            $fetch = $this->addToDB($employee);
        
            return response()->json(['message' => 'add employee successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
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

    public function deleteEmployee($employeeNumber){
        $user = auth()->user();

        // if($user->tokenCan('HR')){    real
        if($user->tokenCan('Employee')){
            $inputArr = ['employeeNumber' => $employeeNumber];

            $validator = Validator::make($inputArr ,[
                'employeeNumber' => 'required|exists:employees,employeeNumber',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $targetEmployee = Employee::where('employeeNumber', $employeeNumber)->first();
            $targetEmployee->delete();

            return response()->json(['message' => 'delete employee successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function updateEmployee($employeeNumber, Request $request){
        $user = auth()->user();
        // if($user->tokenCan('VP')){    real
        if($user->tokenCan('HR')){
            $input = $request->except(['employeeNumber', 'jobTitle']);
            $input['employeeNumber'] = $employeeNumber;

            $validator = Validator::make($input, [
                'employeeNumber' => 'required|exists:employees,employeeNumber',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $targetEmployee = Employee::find($employeeNumber);
            $targetEmployee->fill($input)->update();
            return response()->json(['message' => 'upadate employee successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function promoteDemoteEmployee($employeeNumber, Request $request){
        $user = auth()->user();
        // if($user->tokenCan('VP')){
        if($user->tokenCan('HR')){
            $input = $request->all();
            $input['employeeNumber'] = $employeeNumber;

            $validator = Validator::make($input, [
                'employeeNumber' => 'required|exists:employees,employeeNumber',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $targetEmployee = Employee::find($employeeNumber);

            if($this->isAvailable($targetEmployee['jobTitle'], $user->jobTitle)){
                $targetEmployee->fill($input)->update();
                return response()->json(['message' => 'promote-demote employee successfully']);
            }
            else{
                return response()->json(['message' => "you don't have permission to promote-demote this person"], 401);
            }
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function isAvailable($targetEmployeeJobTitle, $requesterJobTitle){

        if($targetEmployeeJobTitle == 'President') $targetRank = 1;
        else if(str_contains($targetEmployeeJobTitle, 'VP')) $targetRank = 2;
        else $targetRank = 3;

        if($requesterJobTitle == 'President') $reqRank = 1;
        else if(str_contains($requesterJobTitle, 'VP')) $reqRank = 2;
        else $reqRank = 3;

        if($reqRank >= $targetRank) return false;
        
        return true;
    }
}
