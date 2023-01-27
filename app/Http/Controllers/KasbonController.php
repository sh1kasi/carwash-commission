<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kasbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee_kasbon;

class KasbonController extends Controller
{
    public function index()
    {
        // $kasbon = Kasbon::get();
        $employee_tetap = Employee::where('role', 'Tetap')->get();
        $now = Carbon::now()->timezone('Asia/Jakarta');
        $currentDate = Carbon::parse($now)->format('Y-m-d');
        foreach ($employee_tetap as $data) {
            $employee = Kasbon::where('employee_id', $data->id)->first();
             $resetDate = Carbon::parse($employee->reset_date)->format('Y-m-d');
             $month = Carbon::parse($resetDate)->diffInMonths($currentDate);
             if ($month > 0) {
                $employee->reset_date = $now->setTimeFromTimeString($now->toTimeString());
                $employee->sisa_nominal = $data->kasbon;
                $employee->kasbon_input = null;
                $employee->save();
             }
        }

        // dd($arr_kasbon);

        // foreach ($arr_kasbon as $m) {
            
        // }
        // dd($dj);
        // dd(Kasbon::where('employee_id', $data->id)->get());

        // dd($arr_kasbon);

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
            if ($row->kasbon_input == null) {
                return '- &nbsp; &nbsp; <button class="text-primary btn" type="button" data-bs-toggle="modal" data-bs-target="#inputKasbon" onclick="inputKasbon('.$row->workers->id.')" id="input_kasbon"><i class="fa-solid fa-dollar-sign fa-lg"></i></button>';
            } else {
                // dd($row->kasbon_input);
                return Carbon::parse($row->kasbon_input)->translatedFormat('j F Y'). '&nbsp; &nbsp; <a class="text-primary" type="button" data-bs-toggle="modal" onclick="inputKasbon('.$row->workers->id.')" data-bs-target="#inputKasbon" id="input_kasbon"><i class="fa-solid fa-dollar-sign fa-lg"></i></a>';
            }
        })
        

        ->escapeColumns([])
        ->make(true);

    } 

    public function input_kasbon(Request $request)
    {
        $this->validate($request, [
            'tgl_input' => 'required',
            'nominal' => 'required|max:100  000'
        ]);
        // dd($request);
        // $tgl = Carbon::parse($request->tgl_input);
        $kasbon = Kasbon::find($request->employee_id);
        // dd($kasbon->sisa_nominal);
        $kasbon->kasbon_input = $request->tgl_input;
        $kasbon->sisa_nominal = $kasbon->sisa_nominal - $request->nominal;
        if ($kasbon->sisa_nominal < 0) {
            return redirect('/kasbon')->with('kasbon_empty', 'Jatah kasbon '. $kasbon->workers->name .' telah habis');
        }
        $kasbon->save();
        $kasbon->employees()->attach($request->employee_id, ['nominal' => $request->nominal, 'kasbon_maksimal' => $kasbon->workers->kasbon, 'tanggal_input' => $request->tgl_input]);

        return redirect('/kasbon')->with('success', 'Berhasil menginput kasbon '.$kasbon->workers->name);
    }

    public function kasbon_detail(Request $request)
    {
        $tgl_input = Carbon::parse($request->tgl_input);
        // dd($tgl_input->year);
        // dd(Ca)
        

        $kasbon_employee = Employee_kasbon::where('employee_id', $request->id)
                           ->whereMonth('created_at', $tgl_input->month)
                           ->whereYear('created_at', $tgl_input->year)->get();
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
}
