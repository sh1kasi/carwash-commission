<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employee;
// use Barryvdh\DomPDF\PDF;
use PDF;
use App\Models\Transaction;
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

        // foreach ($employee as $key) {
        //     $count_transaction = Transaction_employee::where('employee_id', $key->id)->groupBy('transaction_id')
        //     ->withCount('employees')->orderBy('employees_count', 'asc')->count();
        // }
        // dd($count_transaction);


        $trashed_employee = Employee::onlyTrashed()->get();
        // dd($trashed_employee);
        // dd($employee);

        $transaction_employee = Transaction_employee::get();

        return view('admin.employeeIndex', compact('employee', 'transaction_employee', 'trashed_employee'));
    }

    public function form_index()
    {
        
        return view('admin.employeeForm');
    }

    public function form_store(Request $request)
    {

        $this->validate($request, [
            'name'=> 'required',
        ]);

        $name = $request->name;
        
        $employee = new Employee;
        $employee->name = $name;
        $employee->save();

        return redirect('/employee')->with('success', 'Berhasil Menambahkan Pegawai');
    }

    public function edit_index($id)
    {
        $employee = Employee::find($id);

        return view('admin.employeeEdit', compact('employee'));

    }

    public function employee_update(Request $request, $id)
    {
        $employee = Employee::find($id);
        
        $this->validate($request, [
            'name'=> 'required',
        ]);

        $name = $request->name;

        $employee->name = $name;
        $employee->save();

        return redirect('/employee')->with('success', 'Berhasil Mengedit Pegawai');
    }

    public function destroy_employee($id)
    {
        $employee = Employee::find($id);
        $cek_relasi = Transaction_employee::where('employee_id', $id)->exists();

        // if ($cek_relasi == true) {
        //     return back()->with('failed', 'Pegawai '. $employee->name . ' Tidak bisa dihapus karena ' .$employee->name. ' masih memiliki transaksi');
        // } else {
        // }
        $employee->delete();


        return back()->with('success', 'Berhasil Mengedit Pegawai');

    }

    public function restore($id)
    {
        // dd($id);
        
        $trashed_employee = Employee::withTrashed()->where('id', $id)->restore();

        return back()->with('success', 'Berhasil me-restore pegawai');
    }

    // public function data($id)
    // {
    //     $transaksi = Transaction::whereHas('employee_transaction', function($query) use($id) {
    //         $query->where('employee_id', $id);
    //     })->with('products')->with('employees');

    //     return Datatables($transaksi)
    //     // ->addColumn('service', function($row) {
    //     //     $service_column = "";
    //     //     foreach ($row->products as $service) {
    //     //         $service_column .= '<ul><li>'.$service->service.'</li></ul>';
    //     //     }
    //     //     return $service_column;
    //     // })
    //     // ->addColumn('tanggal', function($row) {
    //     //     return $row->created_at->translatedFormat('j F Y - H:i:s');
    //     // })
    //     ->escapeColumns([])
    //     ->make(true);
        
    // }

    public function employee_detail($id, Request $request)
    {
        // return $request;

        $from = $request->from;
        $to = $request->to;
        // dd($id);


        $transaction_employee = Transaction_employee::where('employee_id', $id)
        ->groupBy('transaction_id')
        ->with('transactions')
        ->with('employee_products')
        ->get();

        
        $transaction_product = Transaction_employee::where('employee_id', $id)->get();

        // foreach ($transaction_employee as $data) {
        //     foreach ($transaction_product->where('transaction_id', $data->transactions->id) as $key) {
        //         # code...
        //     }
        // }

        // dd($key);





        // foreach ($transaction_employee as $data) {
        //     // dd($data);
        //     $transaction_product = Transaction_employee::where('transaction_id', $data->transaction_id)
        //     ->where('employee_id', $id)
        //     ->where('transaction_id', $data->transactions->id)
        //     ->with('employee_products')
        //     ->orderBy('product_id')
        //     ->get();
        // }
        // dd($transaction_product);
        // // ->groupBy('transaction_id')
        // ->groupBy('product_id')
        // ->with('transactions')
        // ->with('employee_products')
        // ->get();
        // dd($transaction_employee);

        // $transaction_product = Transaction_employee::where('employee_id', $id)
        // ->groupBy('transaction_id')
        // ->groupBy('product_id')
        // ->with('employee_products')
        // ->get();

        $commission = 0;
        foreach ($transaction_employee as $trans_e) {
            $commission += $trans_e->commission;
        }
        // dd($trans_e);



        $employee = Employee::where('id', $id)->get();
        $transaksi = Transaction::whereHas('employee_transaction', function($query) use($id) {
            $query->where('employee_id', $id);
        })->with('products')->with('employees');

        // dd($transaksi->get());

        $transaction = $transaksi->get();

        // dd($transaction);

        foreach ($transaction_employee as $key) {
        }

        if (!empty($request->from)) {
            if ($request->from === $request->to) {
                      $transaction_employee = $key->whereDate('created_at', $request->from)
                     ->groupBy('transaction_id')
                     ->with('transactions')
                     ->with('employee_products')
                     ->whereDate('created_at', $request->from)->get();
            } else {
                $transaction_employee = $key->whereDate('created_at', '>=', $request->from)
                ->whereDate('created_at', '<=', $request->to)
                ->groupBy('transaction_id')
                ->with('transactions')
                ->with('employee_products')
                ->get();
            }
        } else {
            $transaction_employee = Transaction_employee::where('employee_id', $id)
            ->groupBy('transaction_id')
            ->with('transactions')
            ->with('employee_products')
            ->get();
        }

        // if ($from && $to) {
        //     // dd('a');
        //     $transaction_employee = $key->whereDate('created_at', '>=', $from)
        //     ->groupBy('transaction_id')
        //     ->with('transactions')
        //     ->with('employee_products')
        //     ->whereDate('created_at', '<=', $to)->get();
        //     // $transaction_product = Transaction_employee::where('employee_id', $id)->get();

        //     // dd($transaction);
        // } else {
        //     // dd('b');
        //     $transaction_employee = Transaction_employee::where('employee_id', $id)
        //     ->groupBy('transaction_id')
        //     ->with('transactions')
        //     ->with('employee_products')
        //     ->get();
      
        // }

        // dd($transaction_employee);
        

        // $normal_products = $key->products()->where('status', '0')->get();
        // $extra_products = $key->products()->where('status', '1')->get();
        // $normal_workers = $key->employees()->where('status', 'normal')->count();
        // $extra_workers = $key->employees()->where('status', 'extra')->count();
        // $total_workers = $key->employees()->count();

        




        

        // if ($extra_workers != 0 && $normal_workers != 0) {
        //     foreach ($normal_products as $biasa) {
        //         $normal_price = $biasa->price;
        //     }
        //     foreach ($extra_products as $extra) {
        //         $extra_price = $extra->price;
        //     }
        // } elseif ($extra_workers == 0 && $normal_workers != 0) {
        //     foreach ($extra_products as $extra) {
        //         $extra_price = $extra->price;
        //     }
        //     foreach ($normal_products as $biasa) {
        //         $normal_price = $biasa->price;
        //     }
        // } elseif ($extra_workers != 0 && $normal_workers == 0) {
        //     foreach ($normal_products as $biasa) {
        //         $normal_price = $biasa->price;
        //     }
        //     foreach ($extra_products as $extra) {
        //         $extra_price = $extra->price;
        //     }
        // }

        // dd($extra_price);

        // dd($normal_products);

        // dd($key->employees()->where('status', 'normal')->get());


        // $transaction_employee = Transaction_employee::where('employee_id', $id)->get();
       
        $employee = Employee::find($id);
        // if ($employee->trashed()) {
        //     $employee = Employee::onlyTrashed()->where('id', $id)->get();
        // } else {
        //     $employee = Employee::find($id);
        // }

        return view('admin.employeeDetail', compact('transaction', 'transaction_employee', 'transaction_product', 'id', 'employee'));
    }


    public function employee_detail_export($id)
    {
        $employee = Employee::where('id', $id)->first();
        $transaksi = Transaction::whereHas('employee_transaction', function($query) use($id) {
            $query->where('employee_id', $id);
        })->with('products')->with('employees');



        $transaction = $transaksi->get();

        return view('admin.employeeDetailExport', compact('transaction', 'id', 'employee'));
    }

    public function employee_pdf(Request $request)
    {
        $id = $request->employee_id;
        // dd($id);
        $from = $request->from;
        $to = $request->to;

        $employee = Employee::where('id', $id)->first();

        // dd($request);

        $transaction = Transaction::whereHas('employee_transaction', function($query) use($id) {
            $query->where('employee_id', $id);
        })->with('products')->with('employees');

        $transaction_employee = Transaction_employee::where('employee_id', $id)
        ->groupBy('transaction_id')
        ->with('transactions')
        ->with('employee_products')
        ->get();

        $transaction_product = Transaction_employee::where('employee_id', $id)->get();


        foreach ($transaction_employee as $transaksi) {
        }
        // dd($to);
        if ($from && $to) {
            // dd('a');
            $daterange = $transaksi->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->groupBy('transaction_id')
            ->with('transactions')
            ->with('employee_products')
            ->get();
        } else {
            // dd('b');
            $daterange = $transaction_employee = Transaction_employee::where('employee_id', $id)
            ->groupBy('transaction_id')
            ->with('transactions')
            ->with('employee_products')
            ->get();
        }

        // dd(Carbon::now());

        // if ($daterange != null) {
            
        // }

        view()->share([
            'daterange' => $daterange,
            'transaction_product' => $transaction_product,
            'id' => $id,
            'employee' => $employee,
            'from' => $from,
            'to' => $to,
        ]);
        $pdf = PDF::loadview('admin.employeeDetailExportPDF');
        // dd($pdf);
        return $pdf->download('Komisi '. $employee->name .'.pdf');
        // return response()->download($pdf, 'testKomisi.pdf');

    }

    public function employee_date(Request $request)
    {
        $id = $request->id;
        $from = $request->from_date;
        $to = $request->to_date;

        

        $transaction = Transaction::whereHas('employee_transaction', function($query) use($id) {
            $query->where('employee_id', $id);
        })->with('products')->with('employees');

        foreach ($transaction->get() as $transaksi) {
        }
        // dd($to);
        $daterange = $transaksi->where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();

        // dd($daterange);

        view()->share('daterange', $daterange);
        $pdf = PDF::loadview('admin.employeeDetailExportPDF');
        return $pdf->download('testKomisi.pdf');

        // return view('admin.employeeDetailExportPDF', compact('daterange'));

        // dd($daterange);
        
    }
}