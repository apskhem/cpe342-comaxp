<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\authApiController;
use App\Http\Controllers\employeeApiController;
use App\Http\Controllers\productApiController;
use App\Http\Controllers\customerApiController;

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

Route::post('register', [employeeApiController::class, 'createEmployee'])->name('register');

Route::get('product', [productApiController::class, 'showProduct'])->name('product');
Route::post('product', [productApiController::class, 'addProduct']);
Route::delete('product', [productApiController::class, 'deleteProduct']);
Route::post('update-product', [productApiController::class, 'updateProduct']);

Route::post('customer', [customerApiController::class, 'addCustomer'])->name('customer');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('dashboard', [authApiController::class, 'dashboardType'])->name('dashboard');
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
