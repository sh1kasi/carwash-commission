<?php

namespace App\Http\Controllers;

use App\Models\Transaction_latests;
use App\Models\Transaksi;

use Illuminate\Http\Request;

class TransaksiController extends Controller
{
            public function index(){
                
                return view('Admin.transaksiIndex');
            }

            public function json(Request $request)
            {
                $transaction_latest = Transaction_latests::get();

                return Datatables($transaction_latest)
                ->addColumn('keterangan', function ($row) {
                    if ($row->keterangan === "pending") {
                        return '<button class="btn btn-warning" id="pending'.$row->id.'" data-date="'.$row->created_at->format('Y-m-d').'" data-transaksiId="'.$row->id.'" data-nopol="'.$row->nopol.'" onclick="transactionInput('.$row->id.')">Belum Dikerjakan</button>';
                    } else {
                        return "<button disabled class='btn btn-success'>Telah Dikerjakan</button>";
                    }
                })
                ->addColumn('date', function ($row) {
                    return $row->created_at->translatedFormat('j F Y');
                })
                ->escapeColumns([])
                ->addIndexColumn()
                ->make(true);
            }
            
}
