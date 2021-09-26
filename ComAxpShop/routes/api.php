<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\authApiController;
use App\Http\Controllers\employeeApiController;
use App\Http\Controllers\productApiController;
use App\Http\Controllers\customerApiController;
use App\Http\Controllers\orderApiController;

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

Route::get('login', [authApiController::class, 'login'])->name('login');
Route::post('login', [authApiController::class, 'checkLogin']);

Route::get('employee', [employeeApiController::class, 'getEmployee'])->name('employee');
Route::post('employee', [employeeApiController::class, 'createEmployee']);
Route::delete('employee', [employeeApiController::class, 'deleteEmployee']);
Route::post('update-employee', [employeeApiController::class, 'updateEmployee']);

Route::get('product', [productApiController::class, 'getProduct'])->name('product');
Route::post('product', [productApiController::class, 'addProduct']);
Route::delete('product', [productApiController::class, 'deleteProduct']);
Route::post('update-product', [productApiController::class, 'updateProduct']);

Route::get('customer', [customerApiController::class, 'getCustomer'])->name('customer');
Route::post('customer', [customerApiController::class, 'addCustomer']);
Route::delete('customer', [customerApiController::class, 'deleteCustomer']);
Route::post('update-customer', [customerApiController::class, 'updateCustomer']);

Route::get('order', [orderApiController::class, 'getAllTransaction'])->name('order');
Route::post('order', [orderApiController::class, 'addTransaction']);

Route::post('increase-point', [customerApiController::class, 'increasePoint']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('dashboard', [authApiController::class, 'dashboardType'])->name('dashboard');
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
