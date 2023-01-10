<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Imports\CustomersImport;
// use App\Http\Controllers\Controller;
// use Maatwebsite\Excel\Facades\Excel;

// class CustomerController extends Controller
// {
//     public function index()
//     {
//         return view('admin.customer');
//     }

//     public function importExcel(Request $request)
//     {
//        $data = $request->file('importExcel');
//        $file_name = $data->getClientOriginalName();
//        $data->move('CustomerData', $file_name);

//        $json = Excel::import(new CustomersImport, \public_path('/CustomerData/'.$file_name));
       
//        return $json;
//     }
// }
