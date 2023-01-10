<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $transaction = Transaction::get();
        $product = Product::get();
        $employees = Employee::get();

        return view('admin.transactionIndex', compact('transaction', 'product', 'employees'));
    }

    public function total_price_check(Request $request)
    {
        $service = $request->serviceArray;

        if ($service == null) {
            return response()->json([
                'status' => 0
            ]);
        } else {
            
            
            // dd($service);
            $product = Product::whereIn('id', $service)->get();
            $product_price = 0;
            
            foreach ($product as $data) {
                $product_price += $data->price;
            }
            
            return response()->json([
                'status' => 1,
                'total_price' => $product_price,
            ]);
        }

    }

    public function transaction_store(Request $request)
    {
        $nopol = $request->nopol;
        $service = $request->service;
        $employee = $request->employee;
        $total_price = $request->total_price;

        

        $transaction = new Transaction;
        $transaction->customer = $nopol;
        $transaction->total_price = $total_price;
        $transaction->comission = $total_price * 30/100;
        $transaction->save();

        
        
        $transaction->products()->attach($service);
        $transaction->employees()->attach($employee);
        
        $transaksi = Transaction::where('id', $transaction->id)->first();
        // dd($transaksi->products);
        $tambahan = $transaksi->products()->where('status', '1')->exists();
        return response()->json([
            'data' => $transaction,
            'worker' => $transaksi->employees,
            'tambahan' => $tambahan,
        ]);
        
    }

    public function commission_detail(Request $request)
    {
        $id = $request->id;
        $transaction = Transaction::where('id', $id)->first();
        $total_workers = $transaction->employees->count();
        $worker_commis = $transaction->comission / $total_workers;


        // dd($transaction->products()->where('status', '1')->exists());
        return response()->json([
            'transaction' => $transaction,
            'product' => $transaction->products,
            'worker' => $transaction->employees,
            'tanggal_transaksi' => $transaction->created_at->format('d-m-Y h:i:s'),
            'commission' => $worker_commis,
        ]);
    }
}
