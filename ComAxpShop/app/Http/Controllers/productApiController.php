<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use Validator;

class productApiController extends Controller
{
    public function getProduct(Request $request){
        $type = $request['type'];

        switch($type){
            case 0: // show all PRODUCT
                $products = DB::table('products')
                            ->select('productName', 'buyPrice')
                            ->orderBy('productName', 'asc')
                            ->get();
                return $products;

            case 1: // show by PRODUCT VENDOR
                $productVendor = $request['productVendor'];
                $products = DB::table('products')
                            ->select('productName', 'buyPrice')
                            ->where('productVendor', $productVendor)
                            ->orderBy('productVendor', 'asc')
                            ->get();
                return $products;

            case 2: // show by PRODUCT SCALE
                $productScale = $request['productScale'];
                $products = DB::table('products')
                            ->select('productName', 'buyPrice')
                            ->where('productScale', $productScale)
                            ->orderBy('productVendor', 'asc')
                            ->get();
                return $products;

            case 3: // show by PRODUCT LINE
                $productLine = $request['productLine'];
                $products = DB::table('products')
                            ->select('productName', 'buyPrice')
                            ->where('productLine', $productLine)
                            ->orderBy('productLine', 'asc')
                            ->get();
                return $products;
        }
    }

    public function addProduct(Request $request){
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

    public function deleteProduct(Request $request){
        $validator = Validator::make(request()->all(), [
            'productCode' => 'required|exists:products,productCode',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $input = $request->only('productCode');
        $targetProduct = Product::where('productCode', $input)->first();
        $targetProduct->delete();

        return response()->json(['message' => 'delete product success']);
    }

    public function updateProduct(Request $request){ // product code is not allowed to change

        $validator = Validator::make(request()->all(), [
            'productCode' => 'required|exists:products,productCode',
            'productLine' => 'exists:productlines,productLine',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $input = $request->all();

        $targetProduct = Product::find($input['productCode']);
        $targetProduct->fill($input)->update();

        return response('Data updated');
    }
}

