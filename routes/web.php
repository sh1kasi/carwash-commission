<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

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
Route::get('/transaction/json', [TransactionController::class, 'data'])->name('transaction.json');
Route::post('/transaction-total', [TransactionController::class, 'total_price_check'])->name('transaction.totalCheck');
Route::post('/transaction-store', [TransactionController::class, 'transaction_store'])->name('transaction.store');
Route::post('/transaction-detail', [TransactionController::class, 'commission_detail'])->name('transaction.detail');
Route::post('/transaction-extraworkers', [TransactionController::class, 'extra_workers'])->name('transaction.extra');

// Employee
Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');

// Product
Route::get('/layanan', [ProductController::class, 'index'])->name('product.index');

// Route::post('/customer-import', [CustomerController::class, 'importExcel'])->name('customer.import');