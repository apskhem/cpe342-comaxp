<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;

use App\Models\PreOrder;

class preorderApiController extends Controller
{
    public function getPreOrder(){
        $user = auth()->user();

        if($user->tokenCan('Employee')){
            $preOrders = PreOrder::query()
                            ->orderBy('orderNumber', 'asc')
                            ->get();

            return $preOrders;
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);
        }
    }

    static function addPreOrder($preOrder){
        $user = auth()->user();
        
        if($user->tokenCan('Salesman')){

            $validator = Validator::make($preOrder, [
                'orderNumber' => 'required|exists:orders,orderNumber',
                'upfrontPrice' => 'required|min:0',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 400);
            }

            preorderApiController::addToDB($preOrder);

            return response()->json(['message' => 'add success']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);
        }
        
    }

    static function addToDB($preOrderData){
        return PreOrder::create([
            'orderNumber' => $preOrderData['orderNumber'],
            'upfrontPrice' => $preOrderData['upfrontPrice']
        ]);
    }

    public function deletePreOrder($orderNumber){
        $user = auth()->user();
        
        if($user->tokenCan('Salesman')){
            $input = ['orderNumber' => $orderNumber];

            $validator = Validator::make($input, [
                'orderNumber' => 'required|exists:preorders,orderNumber',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $targetPreOrder = PreOrder::where('orderNumber', $orderNumber)->first();
            $targetPreOrder->delete();

            return response()->json(['messgae' => "delete order number ".$orderNumber." successfully"]);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);

        }
    }

    public function updatePreOrder($orderNumber, Request $request){
        $user = auth()->user();
        
        if($user->tokenCan('Salesman')){
            $input = $request->all();
            $input['orderNumber'] = $orderNumber;

            $validator = Validator::make($input, [
                'orderNumber' => 'required|exists:preorders,orderNumber'
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $input = $request->all();

            $targetPreOrder = PreOrder::find($orderNumber);
            $targetPreOrder->fill($input)->update();

            return response()->json(['message' => 'update pre order successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);
        }
    }
}
