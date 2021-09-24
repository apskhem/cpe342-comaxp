<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authApiController;
use App\Http\Controllers\employeeApiController;

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
// Route::group(['middleware' => 'auth:sanctum'], function (Request $request) {
//     return $request->user();
// });

Route::get('login', [authApiController::class, 'login'])->name('login');
Route::post('login', [authApiController::class, 'checkLogin']);

Route::post('register', [employeeApiController::class, 'createEmployee'])->name('register');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
