<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon;
use Validator;

use App\Models\Order;

class orderApiController extends Controller
{
    public function addTransaction(Request $request){

        $validator = Validator::make(request()->all(), [
            'requiredDate' => 'required|date_format:Y-m-d',
            'shippedDate' => 'date_format:Y-m-d',
            'status' => 'required',
            'comments' => 'nullable',
            'customerNumber' => 'required|exists:customers,customerNumber',
            'discountCode' => 'nullable|exists:discountcodes,discountCode'
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $dateToday = Carbon\Carbon::now()->setTimezone('Asia/Phnom_Penh')->format('Y-m-d');

        $order = $request->all();
        $order['orderDate'] = $dateToday;

        $fetch = $this->addToDB($order);
    
        return response()->json(['object' => $order]);
    }

    public function addToDB($orderData){
        return Order::create([
            'orderDate' => $orderData['orderDate'],
            'requiredDate' => $orderData['requiredDate'],
            'shippedDate' => $orderData['shippedDate'],
            'status' => $orderData['status'],
            'comments' => $orderData['comments'],
            'customerNumber' => $orderData['customerNumber'],
            'discountCode' => $orderData['discountCode'],
          ]);
    }
}
