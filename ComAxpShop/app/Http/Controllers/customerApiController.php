<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Validator;

use App\Models\Customer;

class customerApiController extends Controller
{
    public function getCustomer(){
        $customers = DB::table('customers')
                        ->orderBy('customerNumber', 'asc')
                        ->get();
        return $customers;
    }
    
    public function addCustomer(Request $request){
        $validator = Validator::make(request()->all(), [
            'customerName' => 'required',
            'contactLastName' => 'required',
            'contactFirstName' => 'required',
            'phone' => 'required',
            'addressLine1' => 'required',
            'addressLine2' => 'nullable',
            'city' => 'required',
            'state' => 'nullable',
            'postalCode' => 'nullable',
            'country' => 'required',
            'salesRepEmployeeNumber' => 'nullable',
            'creditLimit' => 'nullable',
            'memberPoint' => 'nullable',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $customer = $request->all();

        if($customer['memberPoint'] == null) $customer['memberPoint'] = 0;

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
            'creditLimit' => $customerData['creditLimit'],
            'memberPoint' => $customerData['memberPoint'],
          ]);
    }

    public function deleteCustomer(Request $request){
        $validator = Validator::make(request()->all(), [
            'customerNumber' => 'required|exists:customers,customerNumber',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $input = $request->only('customerNumber');
        $targetCustomer = Customer::where('customerNumber', $input)->first();
        $targetCustomer->delete();

        return response('delete completed');
    }

    public function updateCustomer(Request $request){
        $validator = Validator::make(request()->all(), [
            'customerNumber' => 'required|exists:customers,customerNumber',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $input = $request->all();

        $targetCustomer = Customer::find($input['customerNumber']);
        $targetCustomer->fill($input)->update();

        return response('Data updated');
    }
    
    // ----- input is not finish yet ------
    public function increasePoint(Request $request){
        $input = $request->all();

        $increasedPoint = (int) ($input['totalSpend']/100)*3;

        $targetCustomer = Customer::find($input['customerNumber']);
        $targetCustomer->memberPoint += $increasedPoint;
        $targetCustomer->update();

        return $increasedPoint;
    }
}
