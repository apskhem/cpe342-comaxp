<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use Validator;

class productApiController extends Controller
{
    public function getCatalog(){
        $products = DB::table('products')
                    ->get();
        return $products;
    }

    public function getCatalogFilterBy($productVendor){
        // if($input['value'] != ""){
        //     $products = DB::table('products')
        //             ->where($input['value'], $attribute)
        //             ->orderBy($attribute, 'asc')
        //             ->get();
        // }

        $products = DB::table('products')
                    ->where('productVendor', $productVendor)
                    ->orderBy('productVendor', 'asc')
                    ->get();
        return $products;
    }

    public function getProductTuple($productCode){
        $user = auth()->user();
        if($user->tokenCan('Employee')){
            $product = Product::query()
                        ->where('productCode', $productCode)
                        ->get();

            return $product;
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);
        }
    }


    public function getProduct(){

        $user = auth()->user();

        if($user->tokenCan('Employee')){
            $products = DB::table('products')
                        ->orderBy('productCode', 'asc')
                        ->get();

            return $products;
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);
        }
    }

    public function addProduct(Request $request){
        $user = auth()->user();
        
        if($user->tokenCan('Salesman')){
            $validator = Validator::make(request()->all(), [
                'productCode' => 'required|min:8|unique:products',
                'productName' => 'required',
                'productLine' => 'required|exists:productlines,productLine',
                'productScale' => 'required' ,
                'productVendor' => 'required',
                'productDescription' => 'required',
                'quantityInStock' => 'required',
                'buyPrice' => 'required',
                'MSRP' => 'required'
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }
            $product = $request->all();

            $fetch = $this->addToDB($product);
        
            return response()->json(['message' => 'add product success']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);
        }
    }

    public function addToDB($productData){
        return Product::create([
            'productCode' => $productData['productCode'],
            'productName' => $productData['productName'],
            'productLine' => $productData['productLine'],
            'productScale' => $productData['productScale'] ,
            'productVendor' => $productData['productVendor'],
            'productDescription' => $productData['productDescription'],
            'quantityInStock' => $productData['quantityInStock'],
            'buyPrice' => $productData['buyPrice'],
            'MSRP' => $productData['MSRP']
          ]);
    }

    public function deleteProduct($productCode){
        $user = auth()->user();
        if($user->tokenCan('Salesman')){

            $input = ['productCode' => $productCode];

            $validator = Validator::make($input, [
                'productCode' => 'required|exists:products,productCode',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $targetProduct = Product::where('productCode', $productCode)->first();
            $targetProduct->delete();

            return response()->json(['message' => 'delete product success']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);
        }
    }

    public function updateProduct($productCode, Request $request){ // product code is not allowed to change
        $user = auth()->user();
        if($user->tokenCan('Salesman')){
            $input = $request->all();
            $input['productCode'] = $productCode;

            $validator = Validator::make($input, [
                'productCode' => 'required|exists:products,productCode',
                'productLine' => 'exists:productlines,productLine',
            ]);

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $targetProduct = Product::find($productCode);
            $targetProduct->fill($input)->update();

            return response()->json(['message' => 'update product success']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 403);
        }
    }

    static function productSold($product){
        $targetProduct = Product::find($product['productCode']);
        $targetProduct['quantityInStock'] -= $product['quantityOrdered'];
        $targetProduct->update();

        return;
    }
}

