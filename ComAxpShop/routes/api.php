<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\authApiController;
use App\Http\Controllers\employeeApiController;
use App\Http\Controllers\productApiController;
use App\Http\Controllers\customerApiController;
use App\Http\Controllers\orderApiController;
use App\Http\Controllers\paymentApiController;

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

// Route::get('/employees', [employeeApiController::class, 'getEmployee'])->name('employee');
// Route::post('/employees', [employeeApiController::class, 'createEmployee']);
// Route::delete('/employees/{employeeNumber}', [employeeApiController::class, 'deleteEmployee']);
// Route::put('/employees/{employeeNumber}', [employeeApiController::class, 'updateEmployee']);

// Route::get('products', [productApiController::class, 'getProduct'])->name('product');
// Route::post('product', [productApiController::class, 'addProduct']);
// Route::delete('product', [productApiController::class, 'deleteProduct']);
// Route::put('product', [productApiController::class, 'updateProduct']);
Route::get('catalogs', [employeeApiController::class, 'getCatalog']);

// Route::get('customer', [customerApiController::class, 'getCustomer'])->name('customer');
// Route::post('customer', [customerApiController::class, 'addCustomer']);
// Route::delete('customer', [customerApiController::class, 'deleteCustomer']);
// Route::put('customer', [customerApiController::class, 'updateCustomer']);

// Route::get('/orders', [orderApiController::class, 'getAllOrder'])->name('order');
// Route::post('/orders', [orderApiController::class, 'addOrder']);
// Route::delete('/orders/{orderNumber}', [orderApiController::class, 'deleteOrder']);
// Route::put('/orders/{orderNumber}', [orderApiController::class, 'updateOrder']);

// Route::get('payment', [paymentApiController::class, 'getAllTransaction'])->name('payment');
// Route::post('payment', [paymentApiController::class, 'addTransaction']);
// Route::delete('payment', [paymentApiController::class, 'deleteTransaction']);
// Route::post('update-payment', [paymentApiController::class, 'updateTransaction']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    // employee
    Route::get('/employees', [employeeApiController::class, 'getEmployee'])->name('employee');
    Route::post('/employees', [employeeApiController::class, 'createEmployee']);
    Route::delete('/employees/{employeeNumber}', [employeeApiController::class, 'deleteEmployee']);
    Route::put('/employees/{employeeNumber}', [employeeApiController::class, 'updateEmployee']);
    Route::put('/employee-management/{employeeNumber}', [employeeApiController::class, 'promoteDemoteEmployee']);

    //product
    Route::get('products', [productApiController::class, 'getProduct'])->name('product');
    Route::post('products', [productApiController::class, 'addProduct']);
    Route::delete('products/{productCode}', [productApiController::class, 'deleteProduct']);
    Route::put('products/{productCode}', [productApiController::class, 'updateProduct']);

    //customer
    Route::get('customers', [customerApiController::class, 'getCustomer'])->name('customer');
    Route::post('customers', [customerApiController::class, 'addCustomer']);
    Route::delete('customers/{customerNumber}', [customerApiController::class, 'deleteCustomer']);
    Route::put('customers/{customerNumber}', [customerApiController::class, 'updateCustomer']);

    //order
    Route::get('/orders', [orderApiController::class, 'getAllOrder'])->name('order');
    Route::post('/orders', [orderApiController::class, 'addOrder']);
    Route::delete('/orders/{orderNumber}', [orderApiController::class, 'deleteOrder']);
    Route::put('/orders/{orderNumber}', [orderApiController::class, 'updateOrder']);

    //payment
    Route::get('payments', [paymentApiController::class, 'getAllTransaction'])->name('payment');
    Route::post('payments', [paymentApiController::class, 'addTransaction']);
    Route::delete('payments/{orderNumber}', [paymentApiController::class, 'deleteTransaction']);
    Route::put('payments/{orderNumber}', [paymentApiController::class, 'updateTransaction']);


    // Route::get('/', [authApiController::class, 'dashboardType'])->name('dashboard');
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
