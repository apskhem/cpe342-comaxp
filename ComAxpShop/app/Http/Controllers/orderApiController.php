<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon;
use Validator;
use CodeController;

use App\Models\Order;
use App\Models\OrderDetail;

class orderApiController extends Controller
{
    public function addTransaction(Request $request){
        $validator = Validator::make(request()->all(), [
            'requiredDate' => 'required|date_format:Y-m-d|after_or_equal:today',
            'shippedDate' => 'date_format:Y-m-d|after_or_equal:today|before_or_equal:requiredDate',
            'status' => 'required',
            'comments' => 'nullable',
            'customerNumber' => 'required|exists:customers,customerNumber',
            'discountCode' => 'nullable|exists:discountcodes,discountCode',
            'productCode' => 'required|exists:products,productCode',
            'quantityOrdered' => 'required|integer',
            'priceEach' => 'required|numeric',
            'orderLineNumber' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $dateToday = Carbon\Carbon::now()->setTimezone('Asia/Phnom_Penh')->format('Y-m-d');

        $order = $request->only(['requiredDate', 'shippedDate', 'status', 
                                'comments', 'customerNumber', 'discountCode']);
        $order['orderDate'] = $dateToday;
        $fetch = $this->addOrderToDB($order);

        $orderDetail = $request->only(['productCode', 'priceEach', 'quantityOrdered', 'orderLineNumber']);
        $orderDetail['orderNumber'] = $fetch['orderNumber'];

        $fetch2 = $this->addOrderDetailToDB($orderDetail);
    
        return response()->json(['message' => 'success']);
    }


    public function addOrderToDB($orderData){
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

    public function addOrderDetailToDB($orderDetailData){
        return OrderDetail::create([
            'orderNumber' => $orderDetailData['orderNumber'],
            'productCode' => $orderDetailData['productCode'],
            'quantityOrdered' => $orderDetailData['quantityOrdered'],
            'priceEach' => $orderDetailData['priceEach'],
            'orderLineNumber' => $orderDetailData['orderLineNumber'],
        ]);
    }
}
