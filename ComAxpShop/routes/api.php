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

Route::get('employee', [employeeApiController::class, 'getEmployee'])->name('employee');
Route::post('employee', [employeeApiController::class, 'createEmployee']);
Route::delete('employee', [employeeApiController::class, 'deleteEmployee']);
Route::put('employee', [employeeApiController::class, 'updateEmployee']);

Route::get('product', [productApiController::class, 'getProduct'])->name('product');
Route::post('product', [productApiController::class, 'addProduct']);
Route::delete('product', [productApiController::class, 'deleteProduct']);
Route::put('product', [productApiController::class, 'updateProduct']);

Route::get('customer', [customerApiController::class, 'getCustomer'])->name('customer');
Route::post('customer', [customerApiController::class, 'addCustomer']);
Route::delete('customer', [customerApiController::class, 'deleteCustomer']);
Route::post('update-customer', [customerApiController::class, 'updateCustomer']);

Route::get('order', [orderApiController::class, 'getAllOrder'])->name('order');
Route::post('order', [orderApiController::class, 'addOrder']);
Route::delete('order', [orderApiController::class, 'deleteOrder']);
Route::post('update-order', [orderApiController::class, 'updateOrder']);

Route::get('payment', [paymentApiController::class, 'getAllTransaction'])->name('payment');
Route::post('payment', [paymentApiController::class, 'addTransaction']);
Route::delete('payment', [paymentApiController::class, 'deleteTransaction']);
Route::post('update-payment', [paymentApiController::class, 'updateTransaction']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('dashboard', [authApiController::class, 'dashboardType'])->name('dashboard');
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
