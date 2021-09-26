<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon;
use Validator;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Http\Controllers\customerApiController;

class orderApiController extends Controller
{
    public function getAllTransaction(){
        $transactions = DB::table('orders')
                        ->join('orderdetails', 'orders.orderNumber', '=', 'orderdetails.orderNumber')
                        ->orderBy('orders.orderNumber', 'asc')
                        ->get();
        return $transactions;
    }

    public function addTransaction(Request $request){
        $products = $request->only(['productCode', 'quantityOrdered', 'priceEach', 'orderLineNumber']);
        $input = $request->except(['productCode', 'quantityOrdered', 'priceEach', 'orderLineNumber']);

        // validate
        
        $this->productValidator($products);

        $generalValidator = Validator::make($input, [
            'requiredDate' => 'required|date_format:Y-m-d|after_or_equal:today',
            'shippedDate' => 'date_format:Y-m-d|after_or_equal:today|before_or_equal:requiredDate',
            'status' => 'required',
            'comments' => 'nullable',
            'customerNumber' => 'required|exists:customers,customerNumber',
            'discountCode' => 'nullable|exists:discountcodes,discountCode',
        ]);

        if($generalValidator->fails()){
            return response()->json(['errors' => $generalValidator->errors()], 401);
        }

        // order

        $dateToday = Carbon\Carbon::now()->setTimezone('Asia/Phnom_Penh')->format('Y-m-d');

        $order = $request->only(['requiredDate', 'shippedDate', 'status', 
                                'comments', 'customerNumber', 'discountCode']);
        $order['orderDate'] = $dateToday;
        $fetch = $this->addOrderToDB($order);

        // order detail and calculate gained point

        $productArr = explode(',', $products['productCode']);
        $quantityArr = explode(',', $products['quantityOrdered']);
        $priceArr = explode(',', $products['priceEach']);
        $orderLineArr = explode(',', $products['orderLineNumber']);
        $productCount = count($productArr);

        $orderNumber = $fetch['orderNumber'];

        $totalPaid = 0;

        for($i = 0; $i < $productCount; $i++){
            $orderDetail = [
                'orderNumber' => $orderNumber,
                'productCode' => $productArr[$i],
                'quantityOrdered' => $quantityArr[$i],
                'priceEach' => $priceArr[$i],
                'orderLineNumber' => $orderLineArr[$i],
            ];
            $this->addOrderDetailToDB($orderDetail);

            $totalPaid += $orderDetail['quantityOrdered']*$orderDetail['priceEach'];
        }

        $passer = [
            'customerNumber' => $order['customerNumber'],
            'totalPaid' => $totalPaid,
        ];
        $pointGained = customerApiController::increasePoint($passer);

        return response()->json(['point gained' => $pointGained]);
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

    public function productValidator($products){
        $productArr = explode(',', $products['productCode']);
        $quantityArr = explode(',', $products['quantityOrdered']);
        $priceArr = explode(',', $products['priceEach']);
        $orderLineArr = explode(',', $products['orderLineNumber']);
        $productCount = count($productArr);


        for($i = 0; $i < $productCount; $i++){
            $product = [
                'productCode' => $productArr[$i],
                'quantityOrdered' => $quantityArr[$i],
                'priceEach' => $priceArr[$i],
                'orderLineNumber' => $orderLineArr[$i],
            ];

            $productValidator = Validator::make($product, [
                'productCode' => 'required|exists:products,productCode',
                'quantityOrdered' => 'required|integer',
                'priceEach' => 'required|numeric',
                'orderLineNumber' => 'required|integer',
            ]);

            if($productValidator->fails()){
                return response()->json(['errors' => $productValidator->errors()], 401);
            }
        }
    }
}
