<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kasbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee_kasbon;
use Yajra\DataTables\Contracts\DataTable;

class KasbonController extends Controller
{

    public function index()
    {
        // dd(Carbon::now()->addDay(7));
        return view('admin.kasbonIndex');
    }

    public function data(Request $request)
    {

        $kasbon = Kasbon::with('employees')->get();

        function rupiah($angka)
        {
            $hasil_rupiah = "Rp " . number_format($angka,0,',','.');
            return $hasil_rupiah;
        }

        // DataTables
        return Datatables($kasbon)
        ->addColumn('name', function($row) {
            $employees = Employee::where('role', '!=', 'Training')->get();
            // dd($row->employees);
            return $row->workers->name ." (".$row->workers->role.")";
            // foreach ($row->employees as $data) {
            // }
        })
        ->addColumn('detail', function($row) {
            return '<button class="btn btn-primary" id="detail_btn" data-bs-toggle="modal" onclick="detailKasbon('.$row->workers->id.', '."'".$row->kasbon_input."'".')" data-bs-target="#detailKasbon">Detail</button>';
        })
        ->addColumn('sisa_nominal', function($row) {
            return rupiah($row->sisa_nominal);
        })
        ->addColumn('promoted_date', function($row) {
            // dd($row->promoted_date->format());
            return Carbon::parse($row->promoted_date)->translatedFormat('j F Y');
        })
        ->addColumn('kasbon_input', function($row) {

            $current_date = Carbon::now();
            $last_input_date = Carbon::parse($row->kasbon_input);

            $input_check = $last_input_date->diffInDays($current_date);
            // dd($input_check);

            if ($input_check > 6) {
                if ($row->kasbon_input == null) {
                    return '- &nbsp; &nbsp; <button class="text-primary btn border-0" type="button" data-bs-toggle="modal" data-bs-target="#inputKasbon" onclick="inputKasbon('.$row->workers->id.')" id="input_kasbon"><i class="fa-solid fa-dollar-sign fa-lg"></i></button>';
                } else {
                    return Carbon::parse($row->kasbon_input)->translatedFormat('j F Y'). '&nbsp; &nbsp; <button class="text-primary btn border-0" type="button" data-bs-toggle="modal" onclick="inputKasbon('.$row->workers->id.')" data-bs-target="#inputKasbon" id="input_kasbon"><i class="fa-solid fa-dollar-sign fa-lg"></i></button>';
                }
            } else {
                if ($row->kasbon_input == null) {
                    return '- &nbsp; &nbsp; <button class="text-primary btn border-0" type="button" data-bs-toggle="modal" data-bs-target="#inputKasbon" onclick="inputKasbon('.$row->workers->id.')" id="input_kasbon"><i class="fa-solid fa-dollar-sign fa-lg"></i></button>';
                } else {
                    return Carbon::parse($row->kasbon_input)->translatedFormat('j F Y'). '&nbsp; &nbsp; <button class="text-primary btn border-0" disabled type="button" data-bs-toggle="modal" onclick="inputKasbon('.$row->workers->id.')" data-bs-target="#inputKasbon" id="input_kasbon"><i class="fa-solid fa-dollar-sign fa-lg"></i></buton>';
                }
            }

            // dd($input_check);

            
        })
        

        ->escapeColumns([])
        ->make(true);

    } 

    public function input_kasbon(Request $request)
    {
        $this->validate($request, [
            'tgl_input' => 'required',
            'nominal' => 'required|max:100000'
        ]);

        $kasbon = Kasbon::where('employee_id', $request->employee_id)->first();
        // dd($kasbon);

        if ($kasbon->workers->role == 'Tetap') {  
            $kasbon->kasbon_input = $request->tgl_input;
            $kasbon->sisa_nominal = $kasbon->sisa_nominal - $request->nominal;
            if ($kasbon->sisa_nominal < 0) {
                return redirect('/kasbon')->with('kasbon_empty', 'Jatah kasbon '. $kasbon->workers->name .' telah habis');
            }
            $kasbon->save();
            $kasbon->employees()->attach($request->employee_id, ['nominal' => $request->nominal, 'kasbon_maksimal' => $kasbon->workers->kasbon, 'tanggal_input' => $request->tgl_input]);
        } else {
            $kasbon->kasbon_input = $request->tgl_input;
            $kasbon->save();
            $kasbon->employees()->attach($request->employee_id, ['nominal' => $request->nominal, 'kasbon_maksimal' => $kasbon->workers->kasbon, 'tanggal_input' => $request->tgl_input]);
        }


        return redirect('/kasbon')->with('success', 'Berhasil menginput kasbon '.$kasbon->workers->name);
    }

    public function kasbon_detail(Request $request)
    {
        $tgl_input = Carbon::parse($request->tgl_input);
        // dd($tgl_input->year);
        // dd(Ca)
        

        $kasbon_employee = Employee_kasbon::where('employee_id', $request->id)->get();
        // dd($kasbon_employee);
        $kasbons = [];
        $total_kasbon = 0;
        foreach ($kasbon_employee as $kasbon) {
            $total_kasbon += $kasbon->nominal;
            $sisa_nominal = $kasbon->kasbon_maksimal - $total_kasbon;
            $tanggal = Carbon::parse($kasbon->tanggal_input)->translatedFormat('j F Y');
            $push['nominal'] = $kasbon->nominal;
            $push['tanggal'] = $tanggal;
            array_push($kasbons, $push);
        }

        // dd($total_kasbon);
        // dd($kasbons);
        
        return response()->json([
            'kasbon_employee' => $kasbons,
            'sisa_nominal' => $sisa_nominal,
        ]);
    }

    public function kasbon_data(Request $request)
    {
        $tgl_input = Carbon::parse($request->tgl_input);

        if ($request->ajax()) {
            if (!empty($request->from_date)) {
                if ($request->from_date === $request->to_date) {
                    $kasbon_employee = Employee_kasbon::where('employee_id', $request->id)->whereDate('tanggal_input', $request->from_date)->get();
                } else {
                    $kasbon_employee = Employee_kasbon::where('employee_id', $request->id)->whereDate('tanggal_input', '>=', $request->from_date)
                                                  ->whereDate('tanggal_input', '<=', $request->to_date)->get();
                }
            } else {
                $kasbon_employee = Employee_kasbon::where('employee_id', $request->id)->get();
            }
        }

        $bon = Kasbon::where('employee_id', $request->id)->first();

        // dd($kasbon_employee);

        $kasbons = [];
        $total_kasbon = 0;
        $sisa_nominal = 0;
        foreach ($kasbon_employee as $kasbon) {
            $total_kasbon += $kasbon->nominal;
            $sisa_nominal = $kasbon->kasbon_maksimal - $total_kasbon;
            $tanggal = Carbon::parse($kasbon->tanggal_input)->translatedFormat('j F Y');
            $push['nominal'] = $kasbon->nominal;
            $push['tanggal'] = $tanggal;
            array_push($kasbons, $push);
        }

        function rp($angka)
        {
            $hasil_rupiah = "Rp " . number_format($angka,0,',','.');
            return $hasil_rupiah;
        }

        if ($sisa_nominal < 0) {
            $sisa_nominal = 0;
        }

        if ($bon->workers->role == 'Tetap') {
            $sisa_nominal = $sisa_nominal;
        } else {
            $sisa_nominal = 0;
        }

        return Datatables($kasbons)
        ->addColumn('tgl_input', function($row) {
            return $row['tanggal'];
        })
        ->addColumn('nominal_kasbon', function($row) {
            return rp($row['nominal']);
        })
        ->with('sisa_kasbon', $sisa_nominal)
        // return datatables($sisa_nominal)
        ->toJson();


        // ->escapeColumns([])
        // ->make(true);

    }
}
