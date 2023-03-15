<?php

namespace App\Http\Controllers;

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
}
