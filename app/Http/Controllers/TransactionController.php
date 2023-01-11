<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction_employee;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TransactionController extends Controller
{
    
    

    public function index()
    {
        $transaction = Transaction::get();
        $product = Product::get();
        $employees = Employee::get();

        $skrg = Carbon::now('Asia/Jakarta');
        $date = Carbon::parse($skrg)->locale('id');
        $date->settings(['formatFunction' => 'translatedFormat']);
        $tgl = $date->format('j F Y');

        // dd($date);
        // dd($transaction->created_at->format('d'));

        return view('admin.transactionIndex', compact('transaction', 'product', 'employees', 'tgl'));
    }
    
    public function data()
    {
        $transaction = Transaction::get();
        // dd($transaction);

        function rupiah($angka)
        {
            $hasil_rupiah = "Rp " . number_format($angka,0,',','.');
            return $hasil_rupiah;
        }

        // DataTables
        return Datatables($transaction)
        ->addColumn('detail', function($row) {
            return '<button class="btn btn-primary" id="commissionDetail" onclick="commissionDetail('.$row->id.')" data-bs-toggle="modal" data-bs-target="#CommissionModal">Detail Komisi</button>';
        })
        ->addColumn('service', function($row) {
            $service_column = "";
            foreach ($row->products as $service) {
                $service_column .= '<ul><li>'.$service->service.'</li></ul>';
            }
            return $service_column;
        })
        ->addColumn('workers', function($row) {
            $employee_column = "";
            foreach ($row->employees as $worker) {
                $employee_column .= '<ul><li>'.$worker->name.'</li></ul>';
            }
            return $employee_column;
        })
        ->addColumn('tanggal', function($row) {
            $date = Carbon::parse($row->create_at)->locale('id');
            $date->settings(['formatFunction' => 'translatedFormat']);
            return $date->format('j F Y - h:i:s');
        })
        ->addColumn('total_price', function($row) {
            
            return rupiah($row->total_price);
        })
        ->escapeColumns([])
        ->make(true);


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
        $transaction->employees()->attach($employee, ['status' => 'normal']);
        
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



        $extra_workers = $transaction->employees()->wherePivot('status', 'extra')->count();
        
        // dd($extra);

        if ($extra_workers > 0) {
            $normal_workers = $total_workers - $extra_workers;
            $normal_workers_commission = $worker_commis / $normal_workers;
            $extra_workers_commission = $transaction->comission - $worker_commis;
            dd($extra_workers_commission);
        }

            // $extraArray = [];
            // foreach ($extra as $key) {
            //     array_push($extraArray, $key->name);
            // }
            // $pivot = Transaction_employee::whereIn('employee_id', $extraArray)->get();
            // return $extraArray;
        
        
        return response()->json([
            'transaction' => $transaction,
            'product' => $transaction->products,
            // 'extra' => $pivot,
            'worker' => $transaction->employees,
            'tanggal_transaksi' => $transaction->created_at->format('d-m-Y h:i:s'),
            'commission' => $worker_commis,
        ]);
    }

    public function extra_workers(Request $request)
    {
        $extra = $request->extra;
        // $transaksi = Transaction::where('id', $request->id)->first();

        $pivot = Transaction_employee::whereIn('employee_id', $extra)->get();

        foreach ($pivot as $key) {
            # code...
        }
        
        return $key->update(['status' => 'extra']);

    }
}
