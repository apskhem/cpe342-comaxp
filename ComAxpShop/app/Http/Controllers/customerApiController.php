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
            // 'addressLine2' => 'nullable',
            'city' => 'required',
            // 'state' => 'nullable',
            // 'postalCode' => 'nullable',
            'country' => 'required',
            // 'salesRepEmployeeNumber' => 'nullable',
            // 'creditLimit' => 'nullable'
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $customer = $request->all();

        $fetch = $this->addToDB($customer);
    
        return response()->json(['message' => $customer]);
    }

    public function addToDB($customerData){
        return Customer::create([
            'customerName' => $customerData['customerName'],
            'contactLastName' => $customerData['contactLastName'],
            'contactFirstName' => $customerData['contactFirstName'],
            'phone' => $customerData['phone'],
            'addressLine1' => $customerData['addressLine1'],
            'addressLine2' => $customerData['addressLine2'],
            'city' => $customerData['city'],
            'state' => $customerData['state'],
            'postalCode' => $customerData['postalCode'],
            'country' => $customerData['country'],
            'salesRepEmployeeNumber' => $customerData['salesRepEmployeeNumber'],
            'creditLimit' => $customerData['creditLimit']
          ]);
    }
}
