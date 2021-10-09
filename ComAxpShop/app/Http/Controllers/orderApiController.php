<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon;
use Validator;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;

class orderApiController extends Controller
{
    public function getAllOrder(){
        $user = auth()->user();
        
        if($user->tokenCan('Employee')){  
            $orders = DB::table('orders')
                            ->join('orderdetails', 'orders.orderNumber', '=', 'orderdetails.orderNumber')
                            ->orderBy('orders.orderNumber', 'asc')
                            ->get();
            return $orders;
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function addOrder(Request $request){
        $user = auth()->user();
        
        if($user->tokenCan('Salesman')){  
            $products = $request->only(['productCode', 'quantityOrdered', 'priceEach', 'orderLineNumber']);
            $input = $request->except(['productCode', 'quantityOrdered', 'priceEach', 'orderLineNumber']);

            $validStatus = ['canceled', 'disputed', 'in process', 'on hold', 'resloved','shipped'];

            // validate
            
            $productValidator = $this->productValidator($products);

            $generalValidator = Validator::make($input, [
                'requiredDate' => 'required|date_format:Y-m-d|after_or_equal:today',
                'shippedDate' => 'date_format:Y-m-d|after_or_equal:today|before_or_equal:requiredDate',
                'status' => 'in:'.implode(',', $validStatus),
                'comments' => 'nullable',
                'customerNumber' => 'required|exists:customers,customerNumber',
                'discountCode' => 'nullable|exists:discountcodes,discountCode',
            ]);

            if($generalValidator->fails()){
                return response()->json(['errors' => $generalValidator->errors()], 400);
            }

            if($productValidator->fails){
                return response()->json($productValidator->errors, 400);
            }

            // order

            $dateToday = Carbon\Carbon::now()->setTimezone('Asia/Phnom_Penh')->format('Y-m-d');

            $order = $request->only(['requiredDate', 'shippedDate', 'status', 
                                    'comments', 'customerNumber', 'discountCode']);
            $order['orderDate'] = $dateToday;
            $fetch = $this->addOrderToDB($order);

            // // order detail and calculate gained point

            $productArr = explode(',', $products['productCode']);
            $quantityArr = explode(',', $products['quantityOrdered']);
            $priceArr = explode(',', $products['priceEach']);
            $orderLineArr = explode(',', $products['orderLineNumber']);
            $productCount = count($productArr);

            $orderNumber = $fetch['orderNumber'];

            for($i = 0; $i < $productCount; $i++){
                $orderDetail = [
                    'orderNumber' => $orderNumber,
                    'productCode' => $productArr[$i],
                    'quantityOrdered' => $quantityArr[$i],
                    'priceEach' => $priceArr[$i],
                    'orderLineNumber' => $orderLineArr[$i],
                ];
                $this->addOrderDetailToDB($orderDetail);
            }

            return response()->json(['message' => 'add order success']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
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

    public function deleteOrder($orderNumber){
        $user = auth()->user();
        
        if($user->tokenCan('Salesman')){  
            $inputArr = ['orderNumber' => $orderNumber];

            $validator = Validator::make($inputArr, [
                'orderNumber' => 'required|exists:orders,orderNumber',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $targetOrder = Order::where('orderNumber', $orderNumber)->first();
            $targetOrder->delete();

            return response()->json(['message' => 'delete order successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function updateOrder($orderNumber, Request $request){
        $user = auth()->user();
        
        if($user->tokenCan('Salesman')){  
            $validStatus = ['canceled', 'disputed', 'in process', 'on hold', 'resloved', 'shipped'];

            $input = $request->all();
            $input['orderNumber'] = $orderNumber;

            $validator = Validator::make($input, [
                'orderNumber' => 'required|exists:orders,orderNumber',
                'status' => 'in:'.implode(',', $validStatus),
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $targerOrder = Order::find($orderNumber);
            $targerOrder->fill($input)->update();

            return response()->json(['message' => 'update order successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
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

            $returnedObject = (object) [
                'fails' => false,
                'errors',
            ];

            if($productValidator->fails()){
                $returnedObject->fails = true;
                $returnedObject->errors = $productValidator->errors();
                return $returnedObject;
            }

            $isAvailable = $this->isProductAvailable($product);
            if(!$isAvailable){
                $returnedObject->fails = true;
                $returnedObject->errors = ['quantityOrdered' => "Product amount is not enough for purchase"];
                return $returnedObject;
            }
        }
        return $returnedObject;
    }

    public function isProductAvailable($product){
        $targetProduct = Product::find($product['productCode']);

        if($targetProduct->quantityInStock - $product['quantityOrdered'] < 0){
            return false;
        }
        return true;
    }
}

