<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Bundling;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\Bundling_product;
use App\Models\Product_transaction;
use App\Models\Transaction_employee;
use App\Models\Transaction_latests;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;


class TransactionController extends Controller
{
    
    

    public function index(Request $request)
    {
        // dd($request);
        if($request->transaksi_id){
            $t = Transaction_latests::find($request->transaksi_id);
            if($t->keterangan == 'success'){
                return redirect()->route('transaction.index');
            }
        }
        $currentDate = Carbon::now()->format('Y-m-d');

        // dd(Carbon::now()->toTimeString());

        // dd($to);

        $transaction = Transaction::get();
        $product = Product::get();
        $bundle = Bundling::get();

        $arr = [];
        $bundleArray = 0;
        foreach ($bundle as $key) {
            foreach ($key->products as $item) {
                array_push($arr, $item->id);

                $bundleArray = implode(',',$arr);

            }
        }

        // dd($bundleArray);

        $employees = Employee::get();

        $skrg = Carbon::now('Asia/Jakarta');
        $hari = Carbon::parse($skrg)->locale('id');
        $hari->settings(['formatFunction' => 'translatedFormat']);
        $tgl = $hari->format('j F Y');

        // dd($date);
        // dd($transaction->created_at->format('d'));

        return view('admin.transactionIndex', compact('transaction', 'product', 'employees', 'tgl', 'bundle', 'bundleArray', 'currentDate'));
    }

    
    public function data(Request $request)
    {
        if ($request->ajax()) {
            // dd('gaada');
            if (!empty($request->from_date)) {
                if ($request->from_date === $request->to_date) {
                    $transaction = Transaction::whereDate('created_at', $request->from_date)->get();
                } else {
                    $transaction = Transaction::whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->to_date)->get();
                }
            } else {
                // dd('ada');
                $transaction = Transaction::all();
            }
        } 



        // dd($transaction);

        function rupiah($angka)
        {
            $hasil_rupiah = "Rp " . number_format($angka,0,',','.');
            return $hasil_rupiah;
        }

        // DataTables
        return Datatables($transaction)
        ->addColumn('detail', function($row) {
            return '<button class="btn btn-primary" id="commissionDetail" onclick="commissionDetail('.$row->id.')" data-bs-toggle="modal" data-bs-target="#CommissionModal">Detail Komisi</button>';
        })
        ->addColumn('service', function($row) {
            $service_column = "";
            foreach ($row->products as $service) {
                $service_column .= '<ul><li>'.$service->service.'</li></ul>';
            }
            return $service_column;
        })
        ->addColumn('workers', function($row) {
            $employee_column = "";
            $transaction_employee = Transaction_employee::where('transaction_id', $row->id)->orderBy('id', 'ASC')->groupBy('employee_id')->get();
            foreach ($transaction_employee as $worker) {
                $employee_column .= '<ul><li>'.$worker->employees->name.'</li></ul>';
            }
            return $employee_column;
        })
        ->addColumn('tanggal', function($row) {
            return $row->created_at->translatedFormat('j F Y - H:i:s');
        })
        ->addColumn('total_price', function($row) {
            
            return rupiah($row->total_price);
        })
        ->addColumn('aksi', function($row) {
            
            return "<a href='/transaction/edit/$row->id' type='button' class='btn btn-primary' id='transactionEdit'><i>Edit Transaksi</i></a>";
        })
        ->with(['total_transaksi' => $transaction->sum('total_price'), 'total_komisi' => $transaction->sum('comission')])
        ->escapeColumns([])
        ->make(true);


    }

    public function total_price_check(Request $request)
    {
        
        $service = [];
        $bundling = $request->bundling;
        // dd($bundling);

        // dd($bundling_array);

        if (!empty($request->bundling)) {
            $product_bundling = Bundling_product::whereIn('bundling_id', $bundling)->get();
            // dd($product_bundling);
            $bundling_array = [];
            foreach ($product_bundling as $data) {
                array_push($bundling_array, $data->product_id);
            }
        }


        
        

        foreach ($request->serviceArray ?? [] as $service_id) {
            array_push($service, $service_id);
        }
        foreach ($bundling_array ?? [] as $service_id) {
            array_push($service, $service_id);
        }
        // if (!empty($request->bundling) && !empty($request->serviceArray)) {
        //     // dd('ada bundling');
           
            // dd($service);

        // } elseif (!empty($request->bundling)) {
        //     $service = $request->bundling;
        // } else {
        //     $service = $request->serviceArray;
        // }

        // dd($service);

        if ($service == null) {
            return response()->json([
                'status' => 0,
                'total_price' => 0,
            ]);
        } else {
            
            
            // dd($service);
            $product = Product::whereIn('id', $service)->get();
            // dd($product);
            $product_price = 0;
            
            foreach ($product as $data) {
                $product_price += $data->price;
            }

            // dd($product_price);
            
            return response()->json([
                'status' => 1,
                'total_price' => $product_price,
            ]);
        }

    }

    public function transaction_store(Request $request)
    {
        // dd(Carbon::parse($request->date));
        $currentTime = Carbon::now()->toTimeString();

        $nopol = $request->nopol;
        $date = Carbon::parse($request->date)->setTimeFromTimeString($currentTime);
        // $service = $request->service;
        $employee = $request->employee;
        $total_price = $request->total_price;
        $bundling = $request->bundling;
        

        // dd($date);


        $validator = Validator::make($request->all(), [
            'nopol' => 'required',
            'employee' => 'required',
        ]); 

        if ($validator->fails()) {
            // dd($validator->messages());
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
                if (!empty($bundling) && !empty($request->service)) {
                    $arrayService = [];
                    $product_bundling = Bundling_product::whereIn('bundling_id', $bundling)->get();
                    // dd($product_bundling);
                     $bundling_array = [];
                    foreach ($product_bundling as $data) {
                        array_push($bundling_array, $data->product_id);
                    }
                    foreach ($request->service as $service_id) {
                        array_push($bundling_array, $service_id);
                    }
                    $service = array_unique($bundling_array);
                } elseif (!empty($bundling)) {
                    $product_bundling = Bundling_product::whereIn('bundling_id', $bundling)->get();
                    // dd($product_bundling);
                     $bundling_array = [];
                    foreach ($product_bundling as $data) {
                        array_push($bundling_array, $data->product_id);
                    }
                    $service = array_unique($bundling_array);
                } else {
                    $service = $request->service;
                }

            // dd(array_unique($service));
                // $service = [];
            
                // foreach ($request->service ?? [] as $service_id) {
                //     array_push($service, $service_id);
                // }
                // foreach ($request->bundling ?? [] as $service_id) {
                //     array_push($service, $service_id);
                // }
                
                // dd($service);
                
                // if (!empty($request->bundling) && !empty($request->service)) {
                //     // dd('ada bundling');
                //     foreach ($request->service as $service_id) {
                //         array_push($service, $service_id);
                //     }
                //     foreach ($request->bundling as $service_id) {
                //         array_push($service, $service_id);
                //     }
                // } elseif (!empty($request->bundling)) {
                //     $service = $request->bundling;
                // } else {
                //     $service = $request->service;
                // }
                
                // dd($service);
                
                // dd(Product::whereIn('id', $service)->get());
                // dd($service);
                
                $selected_product = Product::whereIn('id', $service)->get();

                // dd($selected_product);   
                
                $normal_product = Product::whereIn('id', $service)->where('status', '0')->get();
                $extra_product = Product::whereIn('id', $service)->where('status', '1')->get();
                // dd(count($normal_product));
                
                if (count($normal_product) != 0 && count($extra_product) != 0) {
                    // dd('a');
                    $product = Product::whereIn('id', $service)->where('status', '0')->get();     
                } elseif (count($normal_product) != 0) {
                    // dd('b');
                    $product = Product::whereIn('id', $service)->where('status', '0')->get();     
                } else {
                    // dd('c');
                    $product = Product::whereIn('id', $service)->get();
                }
            
                // dd($product);

            
                $total_workers = count($employee);
                // dd($ggg);
                // dd($ggg / $total_workers);

                $commission_total = 0;
                foreach ($selected_product as $selprod) {
                
                    if ($selprod->type_commission == 'persentase') {
                        $commiss = $selprod->price * $selprod->commission_value / 100;
                        // dd($commiss);
                        $commission_total += $commiss;
                    } else {
                        $commission_total += $selprod->commission_value;
                    }
                
                }
            
                // dd($commission_total);

                // dd(Product::where('id', $request->bundling)->get());

                // dd(array_unique($service));
            
                $transaction = new Transaction;
                $transaction->customer = $nopol;
                $transaction->total_price = $total_price;
                $transaction->comission = $commission_total;
                $transaction->created_at = $date;
                // dd($transaction);
                $transaction->save();
            
                if ($bundling != null) {
                    $transaction->products()->attach(array_unique($service));
                } elseif ($bundling != null && $service != null) {
                    // $transaction->products()->attach($bundling);
                    $transaction->products()->attach(array_unique($service));
                } else {
                    $transaction->products()->attach(array_unique($service));
                }
            
                
                $commission = 0;
                $ggg = 0;
                // dd(count($extra_product));
                if (count($extra_product) != 0 && count($normal_product) != 0) {
                    
                    foreach ($normal_product as $layanan) {
                        // dd($layanan->type_commission);
                        $commission += $layanan->price;
                        // dd($layanan->type_commission);
    
    
                        if ($layanan->type_commission == 'nominal') {
                            $persenan = $layanan->commission_value / $layanan->price * 100;
                            $commission = $layanan->price * $persenan / 100;
                            $ggg = $commission;
                            // dd($commission);
                        } elseif ($layanan->type_commission == 'nominal' && $layanan->type_commission == 'persentase') {
                            // dd('a');
                        } else {
                            // $commission += $layanan->price * $layanan->commission_value / 100; 
                            $commission = $layanan->commission_value / 100 * $layanan->price; 
                            $ggg = $commission;
                        }
                        foreach ($employee as $worker) {
    
                            $transaction->employees()->attach($worker, ['status' => 'normal', 'commission' => $commission / $total_workers, 'product_id' => $layanan->id]);
                        
    
                        }
                    
                        // $commission += $commission; 
                    }

                } elseif (count($extra_product) != 0 && count($normal_product) == 0) {
                    
                    foreach ($extra_product as $layanan) {
                        // dd($layanan->type_commission);
                        $commission += $layanan->price;
                        // dd($layanan->type_commission);
    
    
                        if ($layanan->type_commission == 'nominal') {
                            $persenan = $layanan->commission_value / $layanan->price * 100;
                            $commission = $layanan->price * $persenan / 100;
                            $ggg = $commission;
                            // dd($commission);
                        } elseif ($layanan->type_commission == 'nominal' && $layanan->type_commission == 'persentase') {
                            // dd('a');
                        } else {
                            // $commission += $layanan->price * $layanan->commission_value / 100; 
                            $commission = $layanan->commission_value / 100 * $layanan->price; 
                            $ggg = $commission;
                        }
                        foreach ($employee as $worker) {
    
                            $transaction->employees()->attach($worker, ['status' => 'extra', 'commission' => $commission / $total_workers, 'product_id' => $layanan->id]);
                        
    
                        }
                    
                        // $commission += $commission; 
                    }

                } else {

                    foreach ($normal_product as $layanan) {
                        // dd($layanan->type_commission);
                        $commission += $layanan->price;
                        // dd($layanan->type_commission);
    
    
                        if ($layanan->type_commission == 'nominal') {
                            $persenan = $layanan->commission_value / $layanan->price * 100;
                            $commission = $layanan->price * $persenan / 100;
                            $ggg = $commission;
                            // dd($commission);
                        } elseif ($layanan->type_commission == 'nominal' && $layanan->type_commission == 'persentase') {
                            // dd('a');
                        } else {
                            // $commission += $layanan->price * $layanan->commission_value / 100; 
                            $commission = $layanan->commission_value / 100 * $layanan->price; 
                            $ggg = $commission;
                        }
                        foreach ($employee as $worker) {
    
                            $transaction->employees()->attach($worker, ['status' => 'normal', 'commission' => $commission / $total_workers, 'product_id' => $layanan->id]);
                        
    
                        }
                    
                        // $commission += $commission; 
                    }

                }

                // if (count($extra_product) != 0) {
                    
                //     foreach ($extra_product as $layanan) {
                //         // dd($layanan->type_commission);
                //         $commission += $layanan->price;
                //         // dd($layanan->type_commission);
    
    
                //         if ($layanan->type_commission == 'nominal') {
                //             $persenan = $layanan->commission_value / $layanan->price * 100;
                //             $commission = $layanan->price * $persenan / 100;
                //             $ggg = $commission;
                //             // dd($commission);
                //         } elseif ($layanan->type_commission == 'nominal' && $layanan->type_commission == 'persentase') {
                //             // dd('a');
                //         } else {
                //             // $commission += $layanan->price * $layanan->commission_value / 100; 
                //             $commission = $layanan->commission_value / 100 * $layanan->price; 
                //             $ggg = $commission;
                //         }
                //         foreach ($employee as $worker) {
    
                //             $transaction->employees()->attach($worker, ['status' => 'extra', 'commission' => $commission / $total_workers, 'product_id' => $layanan->id]);
                        
    
                //         }
                    
                //         // $commission += $commission; 
                //     }

                // } else {

                //     foreach ($normal_product as $layanan) {
                //     // dd($layanan->type_commission);
                //     $commission += $layanan->price;
                //     // dd($layanan->type_commission);


                //     if ($layanan->type_commission == 'nominal') {
                //         $persenan = $layanan->commission_value / $layanan->price * 100;
                //         $commission = $layanan->price * $persenan / 100;
                //         $ggg = $commission;
                //         // dd($commission);
                //     } elseif ($layanan->type_commission == 'nominal' && $layanan->type_commission == 'persentase') {
                //         // dd('a');
                //     } else {
                //         // $commission += $layanan->price * $layanan->commission_value / 100; 
                //         $commission = $layanan->commission_value / 100 * $layanan->price; 
                //         $ggg = $commission;
                //     }
                //     foreach ($employee as $worker) {

                //         $transaction->employees()->attach($worker, ['status' => 'normal', 'commission' => $commission / $total_workers, 'product_id' => $layanan->id]);
                    

                //     }
                
                //     // $commission += $commission; 
                // }

                // }

                
            
            
            
                // $product_extra = Product::whereIn('')
                // dd($total_workers);
            
                $employee_commission = $ggg / $total_workers;
                // dd($employee_commission);

                // if (count($extra_product) != 0 && count($normal_product) == 0) {
                //     // dd($extra_product);
                //     foreach ($extra_product as $extras) {
                //         $transaction->employees()->attach($employee, ['status' => 'extra', 'commission' => $employee_commission, 'product_id' => $extras->id]);
                //     }
                
                // }
            

            
            
                $transaksi = Transaction::where('id', $transaction->id)->first();
                // dd($transaksi->products);
                $extra_product = $transaksi->products()->where('status', '1')->get();
                // dd($extra_product);
            
                foreach ($extra_product as $extra) {
                }
                
                $employees = Employee::whereIn('id', $employee)->get();

                $tambahan = $transaksi->products()->where('status', '1')->exists();

                if($request->request_transaksi_id){
                    $request_transaksi = Transaction_latests::find($request->request_transaksi_id);
                    $request_transaksi->keterangan = 'success';
                    $request_transaksi->save();
                 }

                if (count($extra_product) != 0 && count($normal_product) != 0) {
                    return response()->json([
                        'data' => $transaction,
                        'worker' => $employees,
                        'tambahan' => $tambahan,
                        'extra_product' => $extra_product,
                    ]);
                } else {
                    return response()->json([
                        'data' => $transaction,
                        'worker' => $employees,
                    ]);
                }

        
             
            }
    }

    public function edit_index($id)
    {

        $transaction = Transaction::find($id);
        
        // dd($transaction);
        
        $product = Product::get();
        $bundle = Bundling::get();
        $employees = Employee::get();

        // $selected_employee = Transaction_employee::where('transaction_id', $id)->groupBy('employee_id')->get();

        $productArray = [];
        $bundleArray = [];
        $employeeArray = [];
        $employee_update = [];
        foreach ($transaction->products as $service) {
            array_push($productArray, $service->id);
        }
        foreach ($bundle as $bundling) {
            array_push($bundleArray, $bundling->id);
        }
        foreach ($transaction->employees as $worker) {
            array_push($employeeArray, $worker->id);
        }

        $employee_value = implode(',',$employeeArray);
        $product_value = implode(',',$productArray);
        // dd($product_value);
        // dd($employeeArray);
        // dd($employee_update);

        return view('admin.transactionEdit', compact('product', 'bundle', 'employees', 'productArray', 'bundleArray', 'employeeArray', 'transaction', 'employee_value', 'product_value'));
    }

    public function transaction_update($id, Request $request)
    {
        // $this->validate($request, [
        //     'customer' => 'required',
        //     'servicesCheckbox' => 'required',
        //     'employee' => 'required',
        // ]);

        // dd($request);


        $transaction = Transaction::find($id);
        $bundling = $request->bundlingsCheckbox;
        $nopol = $request->nopol;
        $employee = $request->employee;
        $date = Carbon::parse($request->date)->setTimeFromTimeString($transaction->created_at->toTimeString());
        $total_price = $request->total_price;
        $detach_employee = explode(',',$request->employee_detach);
        $detach_service = explode(',',$request->product_detach);



        // dd($date);

        if (!empty($bundling) && !empty($request->service)) {
            $arrayService = [];
            $product_bundling = Bundling_product::whereIn('bundling_id', $bundling)->get();
            // dd($product_bundling);
             $bundling_array = [];
            foreach ($product_bundling as $data) {
                array_push($bundling_array, $data->product_id);
            }
            foreach ($request->service as $service_id) {
                array_push($bundling_array, $service_id);
            }
            $service = array_unique($bundling_array);
        } elseif (!empty($bundling)) {
            $product_bundling = Bundling_product::whereIn('bundling_id', $bundling)->get();
            // dd($product_bundling);
             $bundling_array = [];
            foreach ($product_bundling as $data) {
                array_push($bundling_array, $data->product_id);
            }
            $service = array_unique($bundling_array);
        } else {
            $service = $request->service;
        }


        $selected_product = Product::whereIn('id', $service)->get();

        // dd($selected_product);   
        
        $normal_product = Product::whereIn('id', $service)->where('status', '0')->get();
        $extra_product = Product::whereIn('id', $service)->where('status', '1')->get();
        // dd(count($normal_product));
        
        if (count($normal_product) != 0 && count($extra_product) != 0) {
            // dd('a');
            $product = Product::whereIn('id', $service)->where('status', '0')->get();     
        } elseif (count($normal_product) != 0) {
            // dd('b');
            $product = Product::whereIn('id', $service)->where('status', '0')->get();     
        } else {
            // dd('c');
            $product = Product::whereIn('id', $service)->get();
        }
    
        // dd($product);

    
        $total_workers = count($employee);
        // dd($ggg);
        // dd($ggg / $total_workers);

        $commission_total = 0;
        foreach ($selected_product as $selprod) {
        
            if ($selprod->type_commission == 'persentase') {
                $commiss = $selprod->price * $selprod->commission_value / 100;
                // dd($commiss);
                $commission_total += $commiss;
            } else {
                $commission_total += $selprod->commission_value;
            }
        
        }

            $transaction->customer = $nopol;
            $transaction->total_price = $total_price;
            $transaction->comission = $commission_total;
            $transaction->created_at = $date;
            $transaction->save();

            if ($bundling != null) {
                $transaction->products()->detach($detach_service);
                $transaction->products()->attach(array_unique($service));
            } elseif ($bundling != null && $service != null) {
                $transaction->products()->detach($detach_service);
                $transaction->products()->attach(array_unique($service));
            } else {
                $transaction->products()->detach($detach_service);
                $transaction->products()->attach(array_unique($service));
            }

            // dd('test product');

            $commission = 0;
            $ggg = 0;
            // dd(count($normal_product));
            $transaction->employees()->detach($detach_employee);
            if (count($extra_product) != 0 && count($normal_product) != 0) {
                
                foreach ($normal_product as $layanan) {
                    // dd($layanan->type_commission);
                    $commission += $layanan->price;
                    // dd($layanan->type_commission);


                    if ($layanan->type_commission == 'nominal') {
                        $persenan = $layanan->commission_value / $layanan->price * 100;
                        $commission = $layanan->price * $persenan / 100;
                        $ggg = $commission;
                        // dd($commission);
                    } elseif ($layanan->type_commission == 'nominal' && $layanan->type_commission == 'persentase') {
                        // dd('a');
                    } else {
                        // $commission += $layanan->price * $layanan->commission_value / 100; 
                        $commission = $layanan->commission_value / 100 * $layanan->price; 
                        $ggg = $commission;
                    }
                    foreach ($employee as $worker) {

                        // $transaction->employees()->detach($detach_employee);
                        $transaction->employees()->attach($worker, ['status' => 'normal', 'commission' => $commission / $total_workers, 'product_id' => $layanan->id]);
                    

                    }
                
                    // $commission += $commission; 
                }

            } elseif (count($extra_product) != 0 && count($normal_product) == 0) {
                
                foreach ($extra_product as $layanan) {
                    // dd($layanan->type_commission);
                    $commission += $layanan->price;
                    // dd($layanan->type_commission);


                    if ($layanan->type_commission == 'nominal') {
                        $persenan = $layanan->commission_value / $layanan->price * 100;
                        $commission = $layanan->price * $persenan / 100;
                        $ggg = $commission;
                        // dd($commission);
                    } elseif ($layanan->type_commission == 'nominal' && $layanan->type_commission == 'persentase') {
                        // dd('a');
                    } else {
                        // $commission += $layanan->price * $layanan->commission_value / 100; 
                        $commission = $layanan->commission_value / 100 * $layanan->price; 
                        $ggg = $commission;
                    }
                    foreach ($employee as $worker) {

                        // $transaction->employees()->detach($detach_employee);
                        $transaction->employees()->attach($worker, ['status' => 'extra', 'commission' => $commission / $total_workers, 'product_id' => $layanan->id]);
                    

                    }
                
                    // $commission += $commission; 
                }

            } else {

                foreach ($normal_product as $layanan) {
                    // dd($layanan->type_commission);
                    $commission += $layanan->price;
                    // dd($layanan->type_commission);


                    if ($layanan->type_commission == 'nominal') {
                        $persenan = $layanan->commission_value / $layanan->price * 100;
                        $commission = $layanan->price * $persenan / 100;
                        $ggg = $commission;
                        // dd($commission);
                    } elseif ($layanan->type_commission == 'nominal' && $layanan->type_commission == 'persentase') {
                        // dd('a');
                    } else {
                        // $commission += $layanan->price * $layanan->commission_value / 100; 
                        $commission = $layanan->commission_value / 100 * $layanan->price; 
                        $ggg = $commission;
                    }
                    foreach ($employee as $worker) {
                        // dd(array_unique($detach_employee));
                        // $transaction->employees()->detach(array_unique($detach_employee));
                        $transaction->employees()->attach($worker, ['status' => 'normal', 'commission' => $commission / $total_workers, 'product_id' => $layanan->id]);
                    

                    }
                
                    // $commission += $commission; 
                }

            }

            $transaksi = Transaction::where('id', $transaction->id)->first();
                // dd($transaksi->products);
                $extra_product = $transaksi->products()->where('status', '1')->get();
                // dd($extra_product);
            
                foreach ($extra_product as $extra) {
                }
                
                $employees = Employee::whereIn('id', $employee)->get();

                $tambahan = $transaksi->products()->where('status', '1')->exists();
                if (count($extra_product) != 0 && count($normal_product) != 0) {
                    return redirect('/transaction')
                    ->with('data', $transaction)
                    ->with('worker', $employees)
                    ->with('tambahan', $tambahan)
                    ->with('transaction_id', $id)
                    ->with('extra_product', $extra_product);
                } else {
                    // return redirect('/transaction')
                    // ->with('data', $transaction)
                    // ->with('worker', $employees);
                    return redirect('/transaction')->with(['id' => $id]);
                }



        // dd("bisa setengah");

    }

    public function commission_detail(Request $request)
    {
        $id = $request->id;
        $transaction = Transaction::where('id', $id)->first();
        $transaction_employee = Transaction_employee::where('transaction_id', $id)->get();
        // dd($transaction_employee);
        
        $normal_commission = 0;
        
        $total_workers = $transaction->employees->count();
        $normal_workers_commission = $transaction->comission / $total_workers;
        $commiss = $transaction->comission / $total_workers;

       foreach ($transaction->products()->where('status', 1)->get() as $extra_product) {
        # code...
       }



        $extra_workers = $transaction->employees()->wherePivot('status', 'extra')->count();
        $normal_workers = $transaction->employees()->wherePivot('status', 'normal')->count();
        // dd($normal_workers);

        $extra = $transaction->products()->where('status', 1)->get();
        $normal = $transaction->products()->where('status', 0)->get();

        // dd($transaction->products());




        if (count($normal) != 0 && count($extra) != 0) {
            // dd('a');
            $normal_price = 0;
            $extra_price = 0;
            $commission = 0;
            $komisi_biasa = 0;
            $komisi_lebih = 0;

            foreach ($normal as $biasa) {
                $normal_price += $biasa->price;

                // $transaction_employee = Transaction_employee::where('transaction_id', $id)->where('product_id', $biasa->id)->get();
                // dd($transaction_employee);
               
                if ($biasa->type_commission == 'nominal') {
                    $persenan = $biasa->commission_value / $biasa->price * 100;
                    $commission = $biasa->price * $persenan / 100;
                    $komisi_biasa += $commission;
                    // dd($biasa->price * $persenan / 100);
                } elseif ($biasa->type_commission == 'nominal' && $biasa->type_commission == 'persentase') {
                    // dd('a');
                } else {
                    // $commission += $layanan->price * $layanan->commission_value / 100; 
                    $commission = $biasa->commission_value / 100 * $biasa->price; 
                    $komisi_biasa += $commission;
                }
               
            }
            // dd($extra);
            foreach ($extra as $lebih) {
                $extra_price += $lebih->price;

                // dd($lebih);

                $transaction_employee = Transaction_employee::where('transaction_id', $id)->where('product_id', $lebih->id)->with('employee_products')->get();
                    // dd($transaction_employee);
                foreach ($transaction_employee as $te) {
                //  dd($te->employee_products->commission_value); 
                }

                if ($lebih->type_commission == 'nominal') {
                    $persenan = $lebih->commission_value / $lebih->price * 100;
                    $commission = $lebih->price * $persenan / 100;
                    $komisi_lebih += $commission;
                    // dd($komisi_lebih);
                } elseif ($lebih->type_commission == 'nominal' && $lebih->type_commission == 'persentase') {
                    // dd('a');
                } else {
                    // $commission += $layanan->price * $layanan->commission_value / 100; 
                    $commission = $lebih->commission_value / 100 * $lebih->price; 
                    $komisi_lebih += $commission;
                }

            }

            

            // dd($normal_workers);
            $normal_commission = $komisi_biasa / $normal_workers; // komisi orang pekerja biasa
            $extra_commission = $komisi_lebih / $extra_workers; // komisi orang pekerja lebih
            // dd($extra_commission);

        } elseif (count($normal) != 0 && count($extra) == 0) {
            $normal_price = 0;
            $extra_price = 0;
            $commission = 0;
            $komisi_biasa = 0;
            $komisi_lebih = 0;
            foreach ($normal as $biasa) {
                $normal_price += $biasa->price;


                if ($biasa->type_commission == 'nominal') {
                    $persenan = $biasa->commission_value / $biasa->price * 100;
                    $commission = $biasa->price * $persenan / 100;
                    $komisi_biasa += $commission;
                    // dd($persenan);
                } elseif ($biasa->type_commission == 'nominal' && $biasa->type_commission == 'persentase') {
                    // dd('a');
                } else {
                    // $commission += $layanan->price * $layanan->commission_value / 100; 
                    $commission = $biasa->commission_value / 100 * $biasa->price; 
                    $komisi_biasa += $commission;
                }


            }
            foreach ($extra as $lebih) {
            }
            $extra_price = 0;

            $normal_commission = $komisi_biasa / $total_workers; // komisi orang pekerja biasa
            // dd($normal_commission);

        } elseif (count($normal) == 0 && count($extra) != 0) {
            $normal_price = 0;
            $extra_price = 0;
            $commission = 0;
            $komisi_biasa = 0;
            $komisi_lebih = 0;
            foreach ($normal as $biasa) {
            }
            foreach ($extra as $lebih) {
                $extra_price += $lebih->price;


                if ($lebih->type_commission == 'nominal') {
                    $persenan = $lebih->commission_value / $lebih->price * 100;
                    $commission = $lebih->price * $persenan / 100;
                    $komisi_lebih += $commission;
                    // dd($commission);
                } elseif ($lebih->type_commission == 'nominal' && $lebih->type_commission == 'persentase') {
                    dd('a');
                } else {
                    // $commission += $layanan->price * $layanan->commission_value / 100; 
                    $commission = $lebih->commission_value / 100 * $lebih->price; 
                    $komisi_lebih += $commission;
                }


            }
            // $commiss_ini = $extra_price * 30/100; 
            // dd(($extra_price * 30/100) / 5 );
            
            $transaction_commission_total = $transaction->comission;
            // $normal_commission = ($normal_price * 30/100) / $normal_workers ;
            $normal_commission = 0;
            $extra_commission = $komisi_lebih / $extra_workers;
            // dd($extra_price);
        }

        // dd($persenan);




        $extra_workers = $transaction->employees()->wherePivot('status', 'extra')->count();
        
        // dd($extra_workers);
        $employee_transaction = Transaction_employee::where('transaction_id', $transaction->id)
            ->with(['employee_products', 'employees'])->groupBy('employee_id')->get();

        $workers = [];
        foreach ($employee_transaction as $worker) {
            $data['worker'] = $worker->employees->name;
            $data['services'] = Transaction_employee::where('transaction_id', $transaction->id)
            ->with(['employee_products', 'employees'])
            ->where('employee_id', $worker->employee_id)->get();
            array_push($workers, $data);
        }
        // return response()->json($workers);

        // dd($workers);

        // $employee_transaction = Transaction_employee::where('transaction_id', $transaction->id)
        // ->groupBy('employee_id')
        // ->with('transactions')
        // ->with('employees')
        // ->with('employee_products')
        // ->get();

        // $transaction_product = Transaction_employee::where('employee_id', $id)->with('employee_products')->get();

        // foreach ($transaction_employee as $data) {
        //     foreach ($transaction_product->where('transaction_id', $data->transactions->id) as $productnya) {
        //         # code...
        //     }
        // }

        // dd($key);



        $employee_transaction_product = Transaction_employee::where('transaction_id', $transaction->id)
        ->groupBy('product_id')
        ->with(['employee_products', 'employees'])->get();

        if ($extra_workers > 0) {

            $normal_workers = $total_workers - $extra_workers;
            if ($normal_workers == 0) {
                $normal_workers = 1;
            }
            // dd($normal_workers);
            $normal_workers_commission = $commiss / $normal_workers;
            $extra_workers_commission = $transaction->comission - $commiss;
            // dd($normal_commission);

            // dd($transaction->employee()->get());
            

            // dd($employee_transaction);

            return response()->json([
                'transaction' => $transaction,
                'total_worker' => $total_workers,
                'product' => $transaction->products,
                'extra_product' => $extra_product->service,
                'normal_commission' => $normal_commission,
                'persenan' => $persenan,
                // 'per_produk' => $productnya,
                'employee_product' => $transaction_employee,
                'extra_commission' => $extra_commission,
                'normal_price' => $normal_price,
                'extra_price' => $extra_price,
                'commiss_check' => $extra_workers,
                'worker' => $workers,
                'total' =>  Transaction_employee::where('transaction_id', $transaction->id)->sum('commission'),
                'grouped_product' => $employee_transaction_product,
                'tanggal_transaksi' => $transaction->created_at->translatedFormat('j F Y - H:i:s'),
                'commission' => $normal_workers_commission,
            ]);
        } else {

            return response()->json([
                'transaction' => $transaction,
                'total_worker' => $total_workers,
                'product' => $transaction->products,
                'commiss_check' => $extra_workers,
                'normal_commission' => $normal_commission,
                // 'per_produk' => $productnya,
                // 'extra_commission' => $extra_commission,
                'grouped_product' => $employee_transaction_product,
                'normal_price' => $normal_price,
                'extra_price' => $extra_price,
                'worker' => $workers,
                'total' =>  Transaction_employee::where('transaction_id', $transaction->id)->sum('commission'),
                'tanggal_transaksi' => $transaction->created_at->translatedFormat('j F Y - H:i:s'),
                'commission' => $normal_workers_commission,
            ]);

        }
    }

    public function extra_workers(Request $request)
    {

        // dd($request);

        $extra = $request->extra;
        $product_extra = $request->product_extra;

        $product_id = $request->product_id;

        // dd($extra);
        $transaction = Transaction::where('id', $request->transaction_id)->first();

        $employee_transaction = Transaction_employee::where('transaction_id', $request->transaction_id)
        ->where('status', 'normal')
        ->with('employee_products')->first();

        // dd($employee_transaction);
        
        // $normal_commission = 0;
        // foreach ($employee_transaction as $employee_trans) {
        //     dd($employee_trans->employee_products);
        // }

        foreach ($product_id as $key => $extra_product) {
            foreach ($request->{"employee_id_".$key} as $a => $value) {


                $employee_product = Product::find($extra_product);
                // dd($employee_product);


                if ($employee_product->type_commission == 'persentase') {

                    $commission = $employee_product->price * $employee_product->commission_value / 100;
                    $employee_commission = $commission / count($request->{"employee_id_".$key});

                    // dd($biasa->price * $persenan / 100);
                    // dd('a');
                } else {
                    // $commission += $layanan->price * $layanan->commission_value / 100; 
                    $employee_commission = $employee_product->commission_value / count($request->{"employee_id_".$key});
                }

                // dd($employee_commission);

               
                
                // dd(count($request->{"employee_id_".$key}));

                // dd($employee_comission);


                $transaction_employee = new Transaction_employee;
                $transaction_employee->employee_id = $value;
                $transaction_employee->transaction_id = $request->transaction_id;
                $transaction_employee->status = 'extra';
                $transaction_employee->commission = $employee_commission;
                $transaction_employee->product_id = $extra_product;
                $transaction_employee->save();

            }
        }


        // dd($transaction_employee);
        
        // $pivot = Transaction_employee::whereIn('employee_id', $extra)->get();

        // // dd($pivot);
        
        // foreach ($pivot as $key) {
        //     # code...
        // }
        
        // $key->update(['status' => 'extra']);


        // $extra = $transaction->products()->where('status', 1)->get();
        // $normal = $transaction->products()->where('status', 0)->get();
        // $extra_workers = $transaction->employees()->wherePivot('status', 'extra')->count();
        // $normal_workers = $transaction->employees()->wherePivot('status', 'normal')->count();
        // $total_workers = $transaction->employees->count();

        // // dd($extra);

        
        // if (count($normal) != 0 && count($extra) != 0) {
        //     // dd('a');
        //     $normal_price = 0;
        //     $extra_price = 0;
        //     $commission = 0;
        //     $komisi_biasa = 0;
        //     $komisi_lebih = 0;

        //     foreach ($normal as $biasa) {
        //         $normal_price += $biasa->price;


        //         if ($biasa->type_commission == 'nominal') {
        //             $persenan = $biasa->commission_value / $biasa->price * 100;
        //             $commission = $biasa->price * $persenan / 100;
        //             $komisi_biasa += $commission;
        //             // dd($biasa->price * $persenan / 100);
        //         } elseif ($biasa->type_commission == 'nominal' && $biasa->type_commission == 'persentase') {
        //             // dd('a');
        //         } else {
        //             // $commission += $layanan->price * $layanan->commission_value / 100; 
        //             $commission = $biasa->commission_value / 100 * $biasa->price; 
        //             $komisi_biasa += $commission;
        //         }


        //     }
        //     foreach ($extra as $lebih) {
        //         $extra_price += $lebih->price;


        //         if ($lebih->type_commission == 'nominal') {
        //             $persenan = $lebih->commission_value / $lebih->price * 100;
        //             $commission = $lebih->price * $persenan / 100;
        //             $komisi_lebih += $commission;
        //             // dd($commission);
        //         } elseif ($lebih->type_commission == 'nominal' && $lebih->type_commission == 'persentase') {
        //             // dd('a');
        //         } else {
        //             // $commission += $layanan->price * $layanan->commission_value / 100; 
        //             $commission = $lebih->commission_value / 100 * $lebih->price; 
        //             $komisi_lebih += $commission;
        //         }

        //     }
            
            
            // $normal_commission = $komisi_biasa / $total_workers ; // komisi orang pekerja biasa
            // $extra_commission = $komisi_lebih / $extra_workers + $normal_commission; // komisi orang pekerja lebih
            // $transaction->employees()->wherePivot('status', 'extra')
            // ->update(['commission' => $extra_commission]);
            // $transaction->employees()->wherePivot('status', 'normal')
            // ->update(['commission' => $normal_commission]);
        // }

            
        // } elseif (count($normal) != 0 && count($extra) == 0) {
        //     $normal_price = 0;
        //     $extra_price = 0;
        //     $commission = 0;
        //     $komisi_biasa = 0;
        //     $komisi_lebih = 0;

        //     foreach ($normal as $biasa) {
        //         $normal_price += $biasa->price;


        //         if ($biasa->type_commission == 'nominal') {
        //             $persenan = $biasa->commission_value / $biasa->price * 100;
        //             $commission = $biasa->price * $persenan / 100;
        //             $komisi_biasa += $commission;
        //             // dd($biasa->price * $persenan / 100);
        //         } elseif ($biasa->type_commission == 'nominal' && $biasa->type_commission == 'persentase') {
        //             // dd('a');
        //         } else {
        //             // $commission += $layanan->price * $layanan->commission_value / 100; 
        //             $commission = $biasa->commission_value / 100 * $biasa->price; 
        //             $komisi_biasa += $commission;
        //         }


        //     }
        //     foreach ($extra as $lebih) {
        //     }
        //     $extra_price = 0;

        //     $normal_commission = $komisi_biasa / $total_workers; // komisi orang pekerja biasa
        //     $transaction->employees()->wherePivot('status', 'normal')
        //         ->update(['commission' => $normal_commission]);

        // } elseif (count($normal) == 0 && count($extra) != 0) {
        //     $normal_price = 0;
        //     $extra_price = 0;
        //     $commission = 0;
        //     $komisi_biasa = 0;
        //     $komisi_lebih = 0;
        //     foreach ($normal as $biasa) {
        //     }
        //     foreach ($extra as $lebih) {
        //         $extra_price += $lebih->price;


        //         if ($lebih->type_commission == 'nominal') {
        //             $persenan = $lebih->commission_value / $lebih->price * 100;
        //             $commission = $lebih->price * $persenan / 100;
        //             $komisi_lebih += $commission;
        //             // dd($commission);
        //         } elseif ($lebih->type_commission == 'nominal' && $lebih->type_commission == 'persentase') {
        //             // dd('a');
        //         } else {
        //             // $commission += $layanan->price * $layanan->commission_value / 100; 
        //             $commission = $lebih->commission_value / 100 * $lebih->price; 
        //             $komisi_lebih += $commission;
        //         }


        //     }
        //     // $commiss_ini = $extra_price * 30/100; 
        //     // dd(($extra_price * 30/100) / 5 );
            
        //     $transaction_commission_total = $transaction->comission;
        //     // $normal_commission = ($normal_price * 30/100) / $normal_workers ;
        //     $normal_commission = 0;
        //     $extra_commission = $komisi_lebih / $extra_workers + $normal_commission;
        //     // dd($extra_price);
        // }

        // dd($normal_commission .'<-- -->'. $extra_commission);

        return redirect('/transaction')->with(['id' => $request->transaction_id]);

        // return response()->json([
        //     'data' => $transaction
        // ]);

    }

    public function select_nopol(Request $request)
    {
        $customer = Transaction::OrderBy('customer', 'ASC');
        $search = $request->search;

        if ($search) {
            $customer->where('customer', 'LIKE', "%{$search}%");
        }

        // $data = $customer->get();

        // $customer->get();
        return response()->json($customer->groupBy('customer')->get());

    }
}
