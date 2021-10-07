<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use Validator;

class productApiController extends Controller
{
    public function getCatalog(Request $request){
        $type = $request['type'];

        switch($type){
            case 0: // show all PRODUCT
                $products = DB::table('products')
                            ->select('productName', 'buyPrice', 'quantityInStock')
                            ->orderBy('productName', 'asc')
                            ->get();
                return $products;

            case 1: // show by PRODUCT VENDOR
                $productVendor = $request['productVendor'];
                $products = DB::table('products')
                            ->select('productName', 'buyPrice', 'quantityInStock')
                            ->where('productVendor', $productVendor)
                            ->orderBy('productVendor', 'asc')
                            ->get();
                return $products;

            case 2: // show by PRODUCT SCALE
                $productScale = $request['productScale'];
                $products = DB::table('products')
                            ->select('productName', 'buyPrice', 'quantityInStock')
                            ->where('productScale', $productScale)
                            ->orderBy('productVendor', 'asc')
                            ->get();
                return $products;

            case 3: // show by PRODUCT LINE
                $productLine = $request['productLine'];
                $products = DB::table('products')
                            ->select('productName', 'buyPrice', 'quantityInStock')
                            ->where('productLine', $productLine)
                            ->orderBy('productLine', 'asc')
                            ->get();
                return $products;
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
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
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
                return response()->json(['errors' => $validator->errors()], 400);
            }
            $product = $request->all();

            $fetch = $this->addToDB($product);
        
            return response()->json(['message' => 'add product success']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
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
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $targetProduct = Product::where('productCode', $productCode)->first();
            $targetProduct->delete();

            return response()->json(['message' => 'delete product success']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
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
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $targetProduct = Product::find($productCode);
            $targetProduct->fill($input)->update();

            return response()->json(['message' => 'update product success']);
        }
        else{
            return response()->json(['errors' => 'you have no permission to access this page'], 401);
        }
    }

    static function productSold($product){
        $targetProduct = Product::find($product['productCode']);
        $targetProduct['quantityInStock'] -= $product['quantityOrdered'];
        $targetProduct->update();

        return;
    }
}

