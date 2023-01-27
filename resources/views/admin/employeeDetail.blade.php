@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />


{{-- @dd(Request()->query('to')) --}}

<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Detail Pegawai</h5>

                        <div class="daterange input-daterange d-flex justify-content-around pt-3 mb-3">
                            <form class="d-flex justify-content-between" id="date_filter" action="" method="get">
                                <div class="from_date d-flex">                              
                                    <p style="width: 155px">Dari tanggal: </p>
                                    <input type="text" class="form-control mb-3" name="from" value="{{ Request()->query('from') }}" id="from_date">
                                </div>
                                <div class="to_date d-flex ms-5">
                                    <p style="width: 250px">Hingga tanggal: </p>
                                    <input type="text" class="form-control mb-3" name="to" value="{{ Request()->query('to') }}" id="to_date">
                                    <button class="btn btn-primary mb-3 ms-2" type="button" id="search_date"><i class="fa fa-search" aria-hidden="true"></i></i></button>
                                    <button class="btn btn-success mb-3 ms-2" type="button" id="print_pdf"><i class="fa fa-download" aria-hidden="true"></i></button>
                                    <a href="/employee/detail/{{ $id }}" class="btn btn-warning mb-3 ms-2" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                </div>
                            </form>
                        </div>

                        {{-- <a href="#" id="addRow" data-bs-toggle="modal" data-bs-target="#transactionForm" class="btn btn-primary m-b-md">Tambah Transaksi</a> --}}
                        <table id="Tables123" class="display table table-bordered" style="width:100%" align="center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nopol</th>
                                    <th>Layanan yang dikerjakan</th>
                                    <th>Tanggal</th>
                                    <th>Komisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                    $total_commission = 0;
                                @endphp
                                @foreach ($transaction_employee as $data)    
                                {{-- @dd($data); --}}
                                    {{-- @foreach ($data->employees->where('id', $id) as $worker) --}}
                                        <tr id="tbody">
                                            <td>{{ $no++ }}</td>
                                            {{-- @dd($transaction_employee) --}}
                                            <td>{{ $data->transactions->customer }}</td>
                                            <td>
                                                <ul>
                                                    @php
                                                        $row_commission = 0;
                                                        // $commission_total = 0;
                                                    @endphp
                                                    {{-- <li>{{ $data->employee_products->service }}</li> --}}
                                                    @foreach ($transaction_product->where('transaction_id', $data->transactions->id) as $product)
                                                        @php
                                                            $row_commission += $product->commission;
                                                            // $commission_total +=/ $product->commission;
                                                            // dd($commission_total)
                                                        @endphp
                                                        <li>{{ $product->employee_products->service }} (@currency($product->commission))</li>
                                                        @php
                                                            $total_commission += $product->commission;
                                                        @endphp
                                                    @endforeach
                                                    {{-- @if ($worker->pivot->status == 'normal')
                                                        @foreach ($data->products->where('status', 0) as $item)
                                                            <li>
                                                                {{ $item->service }}
                                                            </li>
                                                        @endforeach
                                                    @else
                                                        @foreach ($data->products as $item)
                                                        <li>
                                                            {{ $item->service }}
                                                        </li>
                                                        @endforeach
                                                    @endif --}}
                                                </ul>
                                            </td>
                                            <td style="text-align: center">
                                                {{-- {{ $row_commission }} --}}
                                                {{ $data->transactions->created_at->translatedFormat('j F Y - H:i:s') }}
                                            </td>
                                                {{-- @currency($row_commission) --}}
                                            {{-- <td style="text-align: center">@currency($worker->pivot->commission)</td> --}}
                                                {{-- <td style="text-align: center">
                                                    @if ($worker->pivot->status == "extra")
                                                        @currency($worker->pivot->commission) (@currency($extra_price) x 30% / {{ $extra_workers }})
                                                    @else
                                                        @currency($worker->pivot->commission) (@currency($normal_price) x 30% / {{ $total_workers }} )
                                                    @endif
                                                </td> --}}
                                            <td style="text-align: center">@currency($row_commission)</td>
                                            {{-- <td style="text-align: center">@currency($worker->pivot->commission)</td> --}}
                                        </tr>
                                        
                                            {{-- @dd($total_commission) --}}
                                    
                                    {{-- @endforeach --}}
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" text-align="center"></td>
                                    <td class="table-primary" style="text-align: center">Total : </td>
                                    <td class="table-primary" style="text-align: center">@currency($total_commission)</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>      
            </div>
        </div>
    </div>
</div>

<input type="hidden" value="{{ $id }}" id="employee_id">

<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>



<script>
    $(document).ready( function () {

        $("#search_date").click(function (e) { 
            e.preventDefault();
            
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();
            var id = $("#employee_id").val();
            if (from_date != '' && to_date != '') {
             document.getElementById("date_filter").submit();
            } else {
                toastr.error("Harap isi kedua tanggal tanggal tersebut!");
            }

        });

        $('.input-daterange').datepicker({
            todayBtn: 'linked',
            format: 'yyyy-mm-dd',
            autoclose: true,
        });

        $('#Tables123').DataTable({});

        $("#print_pdf").click(function (e) { 
            e.preventDefault();
            
            var employee_id = $("#employee_id").val();

            var from = $("#from_date").val();
            var to = $("#to_date").val();

            // alert(from);

            // if (from == null) {
            //     var from = {!! Request()->query('from')};
            // }

            // if (to == null) {
            //     var to = {!! Request()->query('to')};
            // }

            // alert(from  to);


            window.open(`/employee-detail/cetak?employee_id=${employee_id}&from=${from}&to=${to}`);

            // $.ajax({
            //     type: "get",
            //     url: "/employee-detail/cetak",
            //     data: {
            //         employee_id: employee_id,
            //         from: from,
            //         to: to,
            //     },
            //     dataType: "dataType",
            //     success: function (response) {
                    
            //     }
            // });

        });

        // $("#search_date").click(function (e) { 
        //     e.preventDefault();

        //     console.log('clicked');
            
        //     var from_date = $("#from_date").val();
        //     var to_date = $("#to_date").val();
        //     var employee_id = $("#employee_id").val();

        //     console.log(employee_id);

        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });

        //     $.ajax({
        //         type: "get",
        //         url: `/employee/detail/${employee_id}`,
        //         data: {
        //             from_date: from_date,
        //             to_date: to_date,
        //             id: employee_id,    
        //         },
        //         dataType: "json",
        //         success: function (response) {

        //             window.location.reload();
        //             console.log(response);
        //             $("#tbody").remove();
        //         }
        //     });
             
        //     console.log(from_date);
        //     console.log(to_date);

        // });


    });
</script>
    
@endsection