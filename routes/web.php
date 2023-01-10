<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use App\Models\Transaction;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create som   ething great!w
|
*/

Route::get('/', function () {
    return redirect()->route('transaction.index');
});


// Transaction
Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
Route::post('/transaction-total', [TransactionController::class, 'total_price_check'])->name('transaction.totalCheck');
Route::post('/transaction-store', [TransactionController::class, 'transaction_store'])->name('transaction.store');
Route::post('/transaction-detail', [TransactionController::class, 'commission_detail'])->name('transaction_detail');

// Route::post('/customer-import', [CustomerController::class, 'importExcel'])->name('customer.import');