<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Validator;
use Carbon;
use Arr;

use App\Models\Payment;
use App\Models\Order;
use App\Http\Controllers\customerApiController;
use App\Http\Controllers\productApiController;

class paymentApiController extends Controller
{
    public function getAllTransaction(){
        $user = auth()->user();
        
        if($user->tokenCan('Employee')){  
            $transactions = DB::table('payments')
                            ->orderBy('orderNumber', 'asc')
                            ->get();

            return $transactions;
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function addTransaction(Request $request){
        $user = auth()->user();
        
        if($user->tokenCan('Salesman')){  
            $validator = Validator::make(request()->all(), [
                'checkNumber' => 'required | unique:payments',
                'amount' => 'required | numeric',
                'orderNumber' => 'required | exists:orders,orderNumber',
                'isSuccess' => 'required | in:'.implode(',', ['true', 'false']),
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $transaction = $request->all(); // array of request

            if($request->isSuccess == 'true'){        // payment success
                $dateToday = Carbon\Carbon::now()->setTimezone('Asia/Phnom_Penh')->format('Y-m-d');

                $transaction['paymentDate'] = $dateToday;

                $transactionOrder = Order::find($transaction['orderNumber']);   // find order of transaction

                $transaction['customerNumber'] = $transactionOrder['customerNumber'];   // add customer number into transaction

                $this->addToDB($transaction);

                // reduce product amount
                $order = Order::find($transaction['orderNumber']);      // find product of order in object
                $productInOrderArr = $order->orderdetail->all();

                $productCount = count($productInOrderArr);

                for($i = 0; $i < $productCount; $i++){
                    productApiController::productSold($productInOrderArr[$i]);
                }

                //change order status
                $order->status = "on hold";
                $order->update();
                
                // increase member point
                $customerData = [
                    'customerNumber' => $transaction['customerNumber'],
                    'totalPaid' => $transaction['amount'],
                ];
                $pointGained = customerApiController::increasePoint($customerData);
            
                return response()->json(['point gained' => $pointGained]);
            } 
            else{           // payment failed

                $order = Order::find($transaction['orderNumber']);
                $order->status = "canceled";
                $order->update();

                return response()->json(['message' => 'order has been canceled']);
            }
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function addToDB($transactionData){
        return Payment::create([
            'customerNumber' => $transactionData['customerNumber'],
            'checkNumber' => $transactionData['checkNumber'],
            'paymentDate' => $transactionData['paymentDate'],
            'amount' => $transactionData['amount'],
            'orderNumber' => $transactionData['orderNumber'],
        ]);
    }

    // ควรทำลบแต้มไหม??????????????????????????????????????????????????
    public function deleteTransaction($orderNumber){
        $user = auth()->user();
        
        if($user->tokenCan('Salesman')){  
            $input = ['orderNumber' => $orderNumber];
        
            $validator = Validator::make($input, [
                'orderNumber' => 'required | exists:payments,orderNumber',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $targetTransaction = Payment::where('orderNumber', $orderNumber)->first();
            $targetTransaction->delete();

            // $passer = [
            //     'customerNumber' => $targetTransaction['customerNumber'],
            //     'totalPaid' => $targetTransaction['amount'],
            // ];
            // $pointGained = customerApiController::increasePoint($passer);

            return response()->json(['message' => 'delete transaction successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function updateTransaction($orderNumber, Request $request){
        $user = auth()->user();
        
        if($user->tokenCan('Salesman')){  

            $input = $request->except(['orderNumber', 'customerNumber', 'checkNumber']);
            $input['orderNumber'] = $orderNumber;

            $validator = Validator::make($input, [
                'orderNumber' => 'required | exists:payments,orderNumber',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $input = $request->all();

            $targetTransaction = Payment::find($orderNumber);
            $targetTransaction->fill($input)->update();

            return response()->json(['message' => 'update transaction successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function test(Request $request){
        $input = $request->all();
        
        // $transactions = DB::table('payments')
        //                 ->find($input)
        //                 ->orderBy('orderNumber', 'asc')
        //                 ->get();

        // $transaction = Payment::find($input);

        $transaction = Payment::where($input, ['customerNumber', 'checkNumber'])->first();

        // return $transactions;
        return $transaction;
    }
}

