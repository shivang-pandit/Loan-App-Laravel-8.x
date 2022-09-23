<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\LoanController;
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

//Admin Login
Route::post('admin/login', [AdminController::class, 'login']);

//Customer Login
Route::post('customer/login', [CustomerController::class, 'login']);

//Admin Routes
Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function () {
    Route::get('admin/profile', [AdminController::class, 'profile']);
    Route::put('loan/{loan_id}/approve', [LoanController::class, 'approveLoan']);
    Route::get('loan/customers_loans', [LoanController::class, 'customersLoan']);
});

//Customer Routes
Route::middleware(['auth:sanctum', 'abilities:customer'])->group(function () {
    Route::get('customer/profile', [CustomerController::class, 'profile']);
    Route::post('loan/apply', [LoanController::class, 'apply']);
    Route::get('customer/loans', [LoanController::class, 'customerLoans']);
    Route::put('loan/{loan_payment_id}/pay', [LoanController::class, 'payInstallment']);
});
