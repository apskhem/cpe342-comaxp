<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\authController;
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


use App\Http\Controllers\customerServiceController;
Route::get('/getProductCatalog', [customerServiceController::class, 'getProductsCatalog']);

Route::resource('employees', employeeController::class);

Route::get('dashboard', [authController::class, 'dashboard']); 
Route::get('login', [authController::class, 'index'])->name('login');
Route::post('custom-login', [authController::class, 'customLogin'])->name('login.custom'); 
Route::get('registration', [authController::class, 'registration'])->name('register-user');
Route::post('custom-registration', [authController::class, 'customRegistration'])->name('register.custom'); 
Route::get('signout', [authController::class, 'signOut'])->name('signout');

