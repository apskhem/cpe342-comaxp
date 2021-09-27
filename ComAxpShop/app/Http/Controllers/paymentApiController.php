<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Validator;
use Carbon;

use App\Models\Payment;
use App\Models\Order;
use App\Http\Controllers\customerApiController;

class paymentApiController extends Controller
{
    public function getAllTransaction(){
        $transactions = DB::table('payments')
                        ->orderBy('orderNumber', 'asc')
                        ->get();

        return $transactions;
    }

    public function addTransaction(Request $request){
        $validator = Validator::make(request()->all(), [
            'checkNumber' => 'required',
            'amount' => 'required | numeric',
            'orderNumber' => 'required | exists:orders,orderNumber',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $dateToday = Carbon\Carbon::now()->setTimezone('Asia/Phnom_Penh')->format('Y-m-d');

        $transaction = $request->all();
        $transaction['paymentDate'] = $dateToday;

        $fetch = Order::find($transaction['orderNumber']);

        $transaction['customerNumber'] = $fetch['customerNumber'];

        $fetch2 = $this->addToDB($transaction);

        $passer = [
            'customerNumber' => $transaction['customerNumber'],
            'totalPaid' => $transaction['amount'],
        ];
        $pointGained = customerApiController::increasePoint($passer);
    
        return response()->json(['point gained' => $pointGained]);
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
    public function deleteTransaction(Request $request){
        $validator = Validator::make(request()->all(), [
            'orderNumber' => 'required|exists:payments,orderNumber',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $input = $request->only('orderNumber');
        $targetTransaction = Payment::where('orderNumber', $input)->first();
        $targetTransaction->delete();

        return response('delete completed');
    }

    public function updateTransaction(Request $request){
        $validator = Validator::make(request()->all(), [
            'orderNumber' => 'required|exists:payments,orderNumber',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $input = $request->all();

        $targetTransaction = Payment::find($input['orderNumber']);
        $targetTransaction->fill($input)->update();

        return response('Data updated');
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

