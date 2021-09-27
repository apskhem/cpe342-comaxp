<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Payment;

class paymentApiController extends Controller
{
    public function getAllTransaction(){
        $transactions = DB::table('payments')
                        ->orderBy('orderNumber', 'asc')
                        ->get();

        return $transactions;
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

