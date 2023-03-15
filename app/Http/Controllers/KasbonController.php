<?php

namespace App\Http\Controllers;

use App\Models\Transaction_employee;
use Carbon\Carbon;
use App\Models\Kasbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee_kasbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Contracts\DataTable;

class KasbonController extends Controller
{
    public function index()
    {
        // $kasbon = Kasbon::get();
        // $employee_tetap = Employee::where('role', 'Tetap')->get();
        // $now = Carbon::now()->timezone('Asia/Jakarta');
        // $currentDate = Carbon::parse($now)->format('Y-m-d');
        // foreach ($employee_tetap as $data) {
        //     $employee = Kasbon::where('employee_id', $data->id)->first();
        //      $resetDate = Carbon::parse($employee->reset_date)->format('Y-m-d');
        //      $month = Carbon::parse($resetDate)->diffInMonths($currentDate);
        //      if ($month > 0) {
        //         $employee->reset_date = $now->setTimeFromTimeString($now->toTimeString());
        //         $employee->sisa_nominal = $data->kasbon;
        //         $employee->kasbon_input = null;
        //         $employee->save();
        //      }
        // }

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
        $request = request();
        if ($request->ajax()) {
            // dd('gaada');
            if (!empty($request->from_date)) {
                if ($request->from_date === $request->to_date) {
                    $kasbon = Kasbon::whereDate('created_at', $request->from_date)->get();
                } else {
                    $kasbon = Kasbon::whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->to_date)->get();
                }
            } else {
                // dd('ada'); 
                $kasbon = Kasbon::all(); 
            } 
            // dd($kasbon);
        } 
    

        function rupiah($angka)
        {
            $hasil_rupiah = "Rp " . number_format($angka,0,',','.');
            return $hasil_rupiah;
        }

        // DataTables
        return Datatables($kasbon)
        ->addColumn('name', function($row) {
            // dd($row->worker?->name);
            $employees = Employee::where('role', '!=', 'Training')->get();
            // dd($row->employees);
            return $row->worker?->name." (".$row->worker?->role.")";
            // foreach ($row->employees as $data) {
            // }
        })
        ->addColumn('detail', function($row) {
            return '<button class="btn btn-primary" id="detail_btn" data-bs-toggle="modal" onclick="detailKasbon('.$row->worker?->id    .', '."'".$row->kasbon_input."'".')" data-bs-target="#detailKasbon">Detail</button>';
        })
        ->addColumn('sisa_nominal', function($row) {
            // if ($row->worker?->role === "Tetap") {
            //     return rupiah($row->sisa_nominal);
            // } elseif ($row->worker?->role === "Freelance") {
            //     $transaction_employee = Transaction_employee::where('employee_id', $row->employee_id)->get();
            //     $komisi = 0;
            //     foreach ($transaction_employee as $commiss) {
            //         $komisi += $commiss->commission;
            //     }
            //     return rupiah($komisi);
            // } else {
            //     $transaction_employee = Transaction_employee::where('employee_id', $row->employee_id)->get();
            //     $komisi = 0;
            //     foreach ($transaction_employee as $commiss) {
            //         $komisi += $commiss->commission;
            //     }
            //     return rupiah($komisi);
            // }
            // return rupiah($row->sisa_nominal);
            return rupiah($row->worker?->rest_kasbon);
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
                    return '- &nbsp; &nbsp; <button class="text-primary btn border-0" type="button" data-bs-toggle="modal" data-bs-target="#inputKasbon" onclick="inputKasbon('.$row->worker?->id.')" id="input_kasbon"><i class="fa-solid fa-dollar-sign fa-lg"></i></button>';
                } else {
                    return Carbon::parse($row->kasbon_input)->translatedFormat('j F Y'). '&nbsp; &nbsp; <button class="text-primary btn border-0" type="button" data-bs-toggle="modal" onclick="inputKasbon('.$row->worker?->id.')" data-bs-target="#inputKasbon" id="input_kasbon"><i class="fa-solid fa-dollar-sign fa-lg"></i></button>';
                }
            } else {
                if ($row->kasbon_input == null) {
                    return '- &nbsp; &nbsp; <button class="text-primary btn border-0" type="button" data-bs-toggle="modal" data-bs-target="#inputKasbon" onclick="inputKasbon('.$row->worker?->id.')" id="input_kasbon"><i class="fa-solid fa-dollar-sign fa-lg"></i></button>';
                } else {
                    return Carbon::parse($row->kasbon_input)->translatedFormat('j F Y'). '&nbsp; &nbsp; <button class="text-danger btn border-0" disabled type="button" data-bs-toggle="modal" onclick="inputKasbon('.$row->worker?->id.')" data-bs-target="#inputKasbon" id="input_kasbon"><i class="fa-solid fa-dollar-sign fa-lg"></i></buton>';
                }
            }    
                })
        ->escapeColumns([])
        ->addIndexColumn()
        ->make(true);
    } 

    public function input_kasbon(Request $request)
    {   
        // dd($request);
        if($request->nominal < 100000){
            $employee = Employee::find($request->employee_id);
            // jika sisa kasbon kurang 
            if($employee->rest_kasbon < $request->nominal){
                return redirect('/kasbon')->with('error', 'Sisa Kasbon Tidak cukup');
                
            }
            // $this->validate($request, [
            //     'tgl_input' => 'required',
            //     'nominal' => 'required|max:100000'
            // ]);
            DB::beginTransaction();
            try {
                $kasbon = Kasbon::where('employee_id', $request->employee_id)->first();
    
                if ($kasbon->worker?->role == 'Tetap') {  
                    $kasbon->kasbon_input = $request->tgl_input;
                    $kasbon->sisa_nominal = $kasbon->sisa_nominal - $request->nominal;
                    if ($kasbon->sisa_nominal < 0) {
                        return redirect()->back()->with('kasbon_empty', 'Jatah kasbon '. $kasbon->worker?->name .' telah habis');
                    }
                    $kasbon->save();
                    $kasbon->employees()->attach($request->employee_id, ['nominal' => $request->nominal, 'kasbon_maksimal' => $kasbon->worker?->kasbon, 'tanggal_input' => $request->tgl_input]);
                } else {
                    $kasbon->kasbon_input = $request->tgl_input;
                    $kasbon->save();
                    $kasbon_maksimal = Transaction_employee::where('employee_id', $request->employee_id)->sum('commission');
                    $kasbon->employees()->attach($request->employee_id, ['nominal' => $request->nominal, 'kasbon_maksimal' => $kasbon_maksimal, 'tanggal_input' => $request->tgl_input]);
                }
                DB::commit();
                return redirect('/kasbon')->with('success', 'Berhasil menginput kasbon '.$kasbon->worker?->name);
            } catch (\Throwable $th) {
                // DB::rollBack();
                throw $th;
            }
        }
        return redirect('/kasbon')->with('error', 'Kasbon Melebihi Batas maksimal');
        
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
        $employee = Employee::find($request->id);
        if($employee->role == 'Tetap'){
            $nominal_rest = Employee::find($request->id)->kasbon;
        }else{  
            $nominal_rest = Transaction_employee::where('employee_id', $request->id)->sum('commission');
        }

        foreach ($kasbon_employee as $kasbon) {
            $total_kasbon += $kasbon->nominal;
            $sisa_nominal = $kasbon->kasbon_maksimal - $total_kasbon;
            $tanggal = Carbon::parse($kasbon->tanggal_input)->translatedFormat('j F Y');
            $push['nominal_kasbon'] = $kasbon->nominal;
            $push['tgl_input'] = $tanggal;
            $push['sisa_kasbon'] = $sisa_nominal;
            array_push($kasbons, $push);
        }
        // $kasbon_employee = Employee_kasbon::where('employee_id', $request->id)
        //                    ->whereMonth('created_at', $tgl_input->month)
        //                    ->whereYear('created_at', $tgl_input->year)->get();
        // $kasbons = [];
        // $total_kasbon = 0;
        // foreach ($kasbon_employee as $kasbon) {
        //     $total_kasbon += $kasbon->nominal;
        //     $sisa_nominal = $kasbon->kasbon_maksimal - $total_kasbon;
        //     $tanggal = Carbon::parse($kasbon->tanggal_input)->translatedFormat('j F Y');
        //     $push['nominal'] = $kasbon->nominal;
        //     $push['tanggal'] = $tanggal;
        //     array_push($kasbons, $push);
        // }

        // dd($total_kasbon);
        // dd($kasbons);
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($kasbon_employee->count()),
            "recordsFiltered" => intval($kasbon_employee->count()),
            "sisa_nominal" => $nominal_rest,
            "data" => $kasbons
        );
        echo json_encode($json_data);
        
        // return response()->json([
        //     'kasbon_employee' => $kasbons,
        //     'sisa_nominal' => $sisa_nominal
        // ]);
    }
    public function kasbon_data(Request $request)
    {
        $tgl_input = Carbon::parse($request->tgl_input);

        if ($request->ajax()) {
            if (!empty($request->from_date)) {
                if ($request->from_date === $request->to_date) {
                    $kasbon_employee = Employee_kasbon::where('employee_id', $request->id)->whereDate('tanggal_input', $request->from_date)->get();
                } else {
                    $kasbon_employee = Employee_kasbon::where('employee_id', $request->id)->whereDate('tanggal_input', '>=', $request->from_date)->whereDate('tanggal_input', '<=', $request->to_date)->get();
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

        if ($bon->worker?->role == 'Tetap') {
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


