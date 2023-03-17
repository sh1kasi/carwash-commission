<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\KasbonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Routing\Route as RoutingRoute;
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
Route::get('/transaction/edit/{id}', [TransactionController::class, 'edit_index'])->name('transaction.edit');
Route::post('/transaction/update/{id}', [TransactionController::class, 'transaction_update'])->name('transaction.update');
Route::get('/transaction/json', [TransactionController::class, 'data'])->name('transaction.json');
Route::post('/transaction-total', [TransactionController::class, 'total_price_check'])->name('transaction.totalCheck');
Route::post('/transaction-store', [TransactionController::class, 'transaction_store'])->name('transaction.store');
Route::post('/transaction-detail', [TransactionController::class, 'commission_detail'])->name('transaction.detail');
Route::post('/transaction-extraworkers', [TransactionController::class, 'extra_workers'])->name('transaction.extra');
Route::post('/transaction-select', [TransactionController::class, 'select_nopol'])->name('transaction.select');

// Employee
Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
Route::get('/employee/form', [EmployeeController::class, 'form_index'])->name('employee.form');
Route::post('/employee/form/post', [EmployeeController::class, 'form_store'])->name('employee.post');
Route::get('/employee/edit/{id}', [EmployeeController::class, 'edit_index'])->name('employee.edit');
Route::post('/employee/update/{id}', [EmployeeController::class, 'employee_update'])->name('employee.update');
Route::get('employee/delete/{id}', [EmployeeController::class, 'destroy_employee'])->name('employee.delete');
Route::get('/employee/restore/{id}', [EmployeeController::class, 'restore'])->name('employee.restore');

Route::get('/employee/json', [EmployeeController::class, 'data'])->name('employee.json');
Route::get('/employee/detail/{id}', [EmployeeController::class, 'employee_detail'])->name('employee.detail');
Route::get('/employee-detail/export/{id}', [EmployeeController::class, 'employee_detail_export'])->name('employee.detail.export');
Route::get('/employee-detail/cetak', [EmployeeController::class, 'employee_pdf'])->name('employee.detail.exportPDF');
Route::post('/employee/detail-date', [EmployeeController::class, 'employee_date'])->name('employee.date');

// Product
Route::get('/layanan', [ProductController::class, 'index'])->name('product.index');
Route::get('/layanan/form', [ProductController::class, 'form_index'])->name('product.form');
Route::post('/layanan/form/post', [ProductController::class, 'product_store'])->name('product.post');
Route::get('/layanan/edit/{id}', [ProductController::class, 'edit_index'])->name('product.edit');
Route::post('/layanan/update/{id}', [ProductController::class, 'product_update'])->name('product.update');
Route::get('/layanan/delete/{id}', [ProductController::class, 'destroy'])->name('product.delete');
Route::get('/layanan/restore/{id}', [ProductController::class, 'restore'])->name('product.restore');

// Bundle
Route::get('/bundle', [BundleController::class, 'index'])->name('bundle.index');
Route::get('/bundle/form', [BundleController::class, 'form_index'])->name('bundle.form');
Route::post('/bundle/form/post', [BundleController::class, 'form_store'])->name('bundle.post');
Route::get('/bundle/edit/{id}', [BundleController::class, 'edit_index'])->name('bundle.edit');
Route::post('/bundle/update/{id}', [BundleController::class, 'bundle_update'])->name('bundle.update');
Route::get('bundle/delete/{id}', [BundleController::class, 'destroy_bundle'])->name('bundle.delete');

// Kasbon
Route::get('/kasbon', [KasbonController::class, 'index'])->name('kasbon.index');
Route::get('/kasbon/json', [KasbonController::class, 'data'])->name('kasbon.data');
Route::post('/kasbon/input', [KasbonController::class, 'input_kasbon'])->name('kasbon.input');
Route::get('/kasbon/detail', [KasbonController::class, 'kasbon_detail'])->name('kasbon.detail');

//latest transaksi
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::get('/transaksi/json', [TransaksiController::class, 'json'])->name('transaksi.data');

// customer
Route::get('/customer/mobil', [CustomerController::class, 'index'])->name('customer.mobil');
Route::get('/customer/motor', [CustomerController::class, 'index'])->name('customer.motor');
Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');

// scanqr
Route::get('/scanqr', function() {
    return view('scanqr');
})->name('scan-qr');
// Route::post('/customer-import', [CustomerController::class, 'importExcel'])->name('customer.import');