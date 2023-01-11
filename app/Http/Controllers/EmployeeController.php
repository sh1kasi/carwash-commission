<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Imports\CustomersImport;
use App\Http\Controllers\Controller;
use App\Models\Transaction_employee;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index()
    {

        $employee = Employee::get();

        $transaction_employee = Transaction_employee::get();
        // $washed = 0;
        // foreach ($transaction_employee as $key) {
            
        // }
        
        // dd($transaction_employee);

    

        return view('admin.employeeIndex', compact('employee', 'transaction_employee'));
    }

    // public function importExcel(Request $request)
    // {
    //    $data = $request->file('importExcel');
    //    $file_name = $data->getClientOriginalName();
    //    $data->move('CustomerData', $file_name);

    //    $json = Excel::import(new CustomersImport, \public_path('/CustomerData/'.$file_name));
       
    //    return $json;
    // }
}
