<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/register', [RegisterController::class, 'store' ]);
Route::post('/login',[LoginController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::resource('wallet', WalletController::class)->only(['index','store','destroy']);
    Route::resource('debt', DebtController::class)->only(['index','store','destroy']);
    Route::resource('budget', BudgetController::class)->only(['index','store','destroy']);
    Route::resource('transaction', TransactionController::class)->only(['index','store','destroy']);
});
