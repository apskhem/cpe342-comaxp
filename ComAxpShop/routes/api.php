<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\authApiController;
use App\Http\Controllers\employeeApiController;
use App\Http\Controllers\productApiController;
use App\Http\Controllers\customerApiController;
use App\Http\Controllers\orderApiController;
use App\Http\Controllers\paymentApiController;
use App\Http\Controllers\discountcodeApiController;
use App\Http\Controllers\preorderApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('login', [authApiController::class, 'checkLogin'])->name('login');

Route::get('catalogs', [productApiController::class, 'getCatalog']);
Route::get('catalogs/{productVendor}', [productApiController::class, 'getCatalogFilterBy']);

// Route::get('preorders', [preorderApiController::class, 'getPreOrder'])->name('preorder');
// Route::post('preorders', [preorderApiController::class, ]);
// Route::delete('preorders', [preorderApiController::class, 'deletePreOrder']);
// Route::put('preorders', [preorderApiController::class, 'updatePreOrder']);


// Route::get('discountcodes', [discountcodeApiController::class, 'getDiscountCode'])->name('discountcode');
// Route::post('discountcodes', [discountcodeApiController::class, 'addDiscountCode']);
// Route::delete('discountcodes/{discountCode}', [discountcodeApiController::class, 'deleteDiscountCode']);
// Route::put('discountcodes/{discountCode}', [discountcodeApiController::class, 'updateDiscountCode']);
// Route::get('is-discountcode-exist/{discountCode}', [discountcodeApiController::class, 'isDiscountCodeAvailable']);
// Route::get('decreased/{discountCode}', [discountcodeApiController::class, 'decreaseDiscountCode']);


Route::group(['middleware' => 'auth:sanctum'], function () {

    // employee
    Route::get('employees', [employeeApiController::class, 'getEmployee'])->name('employee');
    Route::get('employees/{employeeNumber}', [employeeApiController::class, 'getEmployeeTuple']);
    Route::post('employees', [employeeApiController::class, 'createEmployee']);
    Route::delete('employees/{employeeNumber}', [employeeApiController::class, 'deleteEmployee']);
    Route::put('employees/{employeeNumber}', [employeeApiController::class, 'updateEmployee']);
    Route::put('employee-management/{employeeNumber}', [employeeApiController::class, 'promoteDemoteEmployee']);

    // product
    Route::get('products', [productApiController::class, 'getProduct'])->name('product');
    Route::get('products/{productCode}', [productApiController::class, 'getProductTuple']);
    Route::post('products', [productApiController::class, 'addProduct']);
    Route::delete('products/{productCode}', [productApiController::class, 'deleteProduct']);
    Route::put('products/{productCode}', [productApiController::class, 'updateProduct']);

    // customer
    Route::get('customers', [customerApiController::class, 'getCustomer'])->name('customer');
    Route::get('customers/{customerNumber}', [customerApiController::class, 'getCustomerTuple']);
    Route::post('customers', [customerApiController::class, 'addCustomer']);
    Route::delete('customers/{customerNumber}', [customerApiController::class, 'deleteCustomer']);
    Route::put('customers/{customerNumber}', [customerApiController::class, 'updateCustomer']);

    // order
    Route::get('orders', [orderApiController::class, 'getAllOrder'])->name('order');
    Route::get('orders/{orderNumber}', [orderApiController::class, 'getOrderTuple']);
    Route::post('orders', [orderApiController::class, 'addOrder']);
    Route::delete('orders/{orderNumber}', [orderApiController::class, 'deleteOrder']);
    Route::put('orders/{orderNumber}', [orderApiController::class, 'updateOrder']);

    // payment
    Route::get('payments', [paymentApiController::class, 'getAllTransaction'])->name('payment');
    Route::post('payments', [paymentApiController::class, 'addTransaction']);
    Route::delete('payments/{orderNumber}', [paymentApiController::class, 'deleteTransaction']);
    Route::put('payments/{orderNumber}', [paymentApiController::class, 'updateTransaction']);

    // discount code
    Route::get('discountcodes', [discountcodeApiController::class, 'getDiscountCode'])->name('discountcode');
    Route::post('discountcodes', [discountcodeApiController::class, 'addDiscountCode']);
    Route::delete('discountcodes/{discountCode}', [discountcodeApiController::class, 'deleteDiscountCode']);
    Route::put('discountcodes/{discountCode}', [discountcodeApiController::class, 'updateDiscountCode']);
    Route::get('is-discountcode-exist/{discountCode}', [discountcodeApiController::class, 'isDiscountCodeAvailable']);

    //preorder
    Route::get('preorders', [preorderApiController::class, 'getPreOrder'])->name('preorder');
    Route::delete('preorders/{orderNumber}', [preorderApiController::class, 'deletePreOrder']);
    Route::put('preorders/{orderNumber}', [preorderApiController::class, 'updatePreOrder']);



    // Route::get('/', [authApiController::class, 'dashboardType'])->name('dashboard');
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
