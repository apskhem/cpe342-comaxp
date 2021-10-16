<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use Carbon;

use App\Models\DiscountCode;

class discountcodeApiController extends Controller
{
    public function getDiscountCode(){
        $user = auth()->user();

        if($user->tokenCan('Employee')){
            $discountCodes = DiscountCode::query()
                            ->orderBy('discountCode', 'asc')
                            ->get();

            return $discountCodes;
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);
        }
    }

    public function addDiscountCode(Request $request){
        $user = auth()->user();

        if($user->tokenCan('VP Marketing')){
            $validator = Validator::make(request()->all(), [
                'discountCode' => 'required|unique:discountcodes,discountCode|size:8',
                'endDate' => 'required|date_format:Y-m-d|after_or_equal:today',
                'amount' => 'required|integer|min:0',
                'discountPrice' => 'required|integer|min:0',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $dateToday = Carbon\Carbon::now()->setTimezone('Asia/Phnom_Penh')->format('Y-m-d');

            $discountCode = $request->all();
            $discountCode['startDate'] = $dateToday;

            $fetch = $this->addToDB($discountCode);
        
            return response()->json(['message' => 'add discount code successfully']);

        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);
        }
    }

    public function addToDB($discountCodeData){
        return DiscountCode::create([
            'discountCode' => $discountCodeData['discountCode'],
            'startDate' => $discountCodeData['startDate'],
            'endDate' => $discountCodeData['endDate'],
            'amount' => $discountCodeData['amount'],
            'discountPrice' => $discountCodeData['discountPrice']
          ]);
    }

    static function deleteDiscountCode($discountCode){
        $user = auth()->user();
        
        // if($user->tokenCan('VP Marketing') || $user->tokenCan('Salesman')){  
        if($user->tokenCan('Salesman')){  

            $input = ['discountCode' => $discountCode];

            $validator = Validator::make($input, [
                'discountCode' => 'required|exists:discountcodes,discountCode',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $targetDiscountCode = DiscountCode::where('discountCode', $discountCode)->first();
            $targetDiscountCode->delete();

            return response()->json(['message' => 'delete discount code successfully']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    public function updateDiscountCode($discountCode, Request $request){
            $user = auth()->user();

            // if($user->tokenCan('VP Marketing')){  
            if($user->tokenCan('Employee')){  
                $input = $request->all();
                $input['discountCode'] = $discountCode;

                $validator = Validator::make($input, [
                    'discountCode' => 'required|exists:discountcodes,discountCode',
                    'startDate' => 'date_format:Y-m-d',
                    'endDate' => 'date_format:Y-m-d',
                    'amount' => 'integer|min:0',
                    'discountPrice' => 'integer|min:0',
                ]);

                if($validator->fails()){
                    return response()->json(['errors' => $validator->errors()], 401);
                }

                $targetDiscountCode = DiscountCode::find($discountCode);
                $targetDiscountCode->fill($input)->update();

                return response()->json(['message' => 'update discount code successfully']);
            }
            else{
                return response()->json(['errors' => 'you have no permission to access this page'], 401);
            }
    }

    public function isDiscountCodeAvailable($discountCode){
        $user = auth()->user();

        if($user->tokenCan('Salesman')){

            $input = ['discountCode' => $discountCode];

            $validator = Validator::make($input, [
                'discountCode' => 'required|exists:discountcodes,discountCode',
            ]);

            if($validator->fails()){
                return response()->json([
                    'isAvailable' => 'false',
                    'message' => 'this discount code does not exist'
                ]);
            }

            $targetDiscountCode = DiscountCode::find($discountCode);

            return response()->json([
                'isAvailable' => 'true',
                'discountCode' => $targetDiscountCode
            ]);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    static function decreaseDiscountCode($discountCode){
        $targetDiscountCode = DiscountCode::find($discountCode);

        if($targetDiscountCode['amount'] == 1){
            $fetch = discountcodeApiController::deleteDiscountCode($discountCode);

            return;
        }
        else{
            $targetDiscountCode['amount'] -= 1;
            $targetDiscountCode->update();

            return;
        }
    }
}
