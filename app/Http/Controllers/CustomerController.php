<?php

namespace App\Http\Controllers;

use App\Models\Transaction_latests;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request){
        if ($request->path() === "customer/motor") {
            return view('admin.customerMotor');
        }else{
            return view('admin.customerMobil');
        }
    }

    public function store(Request $request)
    {   
        $transaction_latest = new Transaction_latests;
        $transaction_latest->nopol = $request->nopol;
        $transaction_latest->jenis_kendaraan = $request->kendaraan;
        $transaction_latest->keterangan = "pending";
        $transaction_latest->save();

        return back()->with('success', 'Transaksi anda telah berhasil!');
    }
}
