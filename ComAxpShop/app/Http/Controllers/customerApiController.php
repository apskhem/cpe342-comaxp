<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Customer;

class customerApiController extends Controller
{
    public function addCustomer(Request $request){
        $validator = Validator::make(request()->all(), [
            'customerName' => 'required',
            'contactLastName' => 'required',
            'contactFirstName' => 'required',
            'phone' => 'required',
            'addressLine1' => 'required',
            'city' => 'required',
            'country' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $customer = $request->all();

        $fetch = $this->addToDB($customer);
    
        return response()->json(['message' => 'success']);
    }

    public function addToDB($employeeData){
        return Customer::create([
            'customerName' => $employeeData['customerName'],
            'contactLastName' => $employeeData['contactLastName'],
            'contactFirstName' => $employeeData['contactFirstName'],
            'phone' => $employeeData['phone'],
            'addressLine1' => $employeeData['addressLine1'],
            'addressLine2' => $employeeData['addressLine2'],
            'city' => $employeeData['city'],
            'state' => $employeeData['state'],
            'postalCode' => $employeeData['postalCode'],
            'country' => $employeeData['country'],
            'salesRepEmployeeNumber' => $employeeData['salesRepEmployeeNumber'],
            'creditLimit' => $employeeData['creditLimit']
          ]);
    }
}
