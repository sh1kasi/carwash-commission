<?php

namespace App\Http\Controllers;
use App\Models\Transaksi;

use Illuminate\Http\Request;

class TransaksiController extends Controller
{
            public function index(){
                return view('Admin.transaksiIndex');
            }

            
}
