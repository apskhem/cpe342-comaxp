<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\authApiController;
use App\Http\Controllers\employeeApiController;
use App\Http\Controllers\productApiController;
use App\Http\Controllers\customerApiController;
use App\Http\Controllers\orderApiController;
use App\Http\Controllers\paymentApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('employee', [employeeApiController::class, 'getEmployee'])->name('employee');
Route::post('employee', [employeeApiController::class, 'createEmployee']);
Route::delete('employee', [employeeApiController::class, 'deleteEmployee']);
Route::put('employee', [employeeApiController::class, 'updateEmployee']);
