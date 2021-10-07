<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Validator;

use App\Models\Customer;

class customerApiController extends Controller
{
    public function getCustomer(){
        $user = auth()->user();

        if($user->tokenCan('Employee')){
            $customers = DB::table('customers')
                            ->orderBy('customerNumber', 'asc')
                            ->get();
            return $customers;
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }
    
    public function addCustomer(Request $request){
        $user = auth()->user();

        if($user->tokenCan('Employee')){
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
        
            return response()->json(['message' => 'add customer successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
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

    public function deleteCustomer($customerNumber){
        $user = auth()->user();

        if($user->tokenCan('Employee')){

            $input = ['customerNumber' => $customerNumber];

            $validator = Validator::make($input, [
                'customerNumber' => 'required|exists:customers,customerNumber',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $targetCustomer = Customer::where('customerNumber', $customerNumber)->first();
            $targetCustomer->delete();

            return response()->json(['message' => 'delete customer successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function updateCustomer($customerNumber, Request $request){
        $user = auth()->user();

        if($user->tokenCan('Employee')){
            $input = $request->all();
            $input['customerNumber'] = $customerNumber;
            
            $validator = Validator::make($input, [
                'customerNumber' => 'required|exists:customers,customerNumber',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $targetCustomer = Customer::find($customerNumber);
            $targetCustomer->fill($input)->update();

            return response()->json(['message' => 'update customer successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }
    
    static function increasePoint($input){

        $increasedPoint = (int) ($input['totalPaid']/100)*3;

        $targetCustomer = Customer::find($input['customerNumber']);
        $targetCustomer->memberPoint += $increasedPoint;
        $targetCustomer->update();

        return $increasedPoint;
    }
}
