@extends('layouts.admin')

@section('content')




<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cetak Detail Komisi {{ $employee->name }}</h5>

                        <div class="daterange d-flex justify-content-around pt-3 mb-3">
                            <form action="/employee-detail/cetak" method="get">
                                <div class="from_date d-flex">                              
                                    <p style="width: 200px">Dari tanggal: </p>
                                    <input type="date" class="form-control mb-3" name="from_date" id="from_date">
                                </div>
                                    <input type="hidden" value="{{ $id }}" name="employee_id">
                                <div class="to_date d-flex">
                                    <p style="width: 200px">Hingga tanggal: </p>
                                    <input type="date" class="form-control mb-3" name="to_date" id="to_date">
                                    {{-- <button class="btn btn-warning mb-3 ms-2"><i class="fa fa-refresh" aria-hidden="true"></i></button> --}}
                                </div>
                                <button class="btn btn-primary mb-3 ms-2" style=" " type="submit" id="print"><i class="fa fa-download" aria-hidden="true"></i></i></button>
                            </form>
                        </div>
                        
                        {{-- <div class="export_btn">
                            <a href="/employee-detail/export" type="button" class="btn btn-success">Export</a>
                        </div> --}}

                        {{-- <a href="#" id="addRow" data-bs-toggle="modal" data-bs-target="#transactionForm" class="btn btn-primary m-b-md">Tambah Transaksi</a> --}}
                        {{-- <table id="Tables123" class="display table table-bordered" style="width:100%" align="center">
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
                                @foreach ($transaction as $data)    
                                    @foreach ($data->employees->where('id', $id) as $worker)
                                        <tr id="tbody">
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $data->customer }}</td>
                                            <td>
                                                <ul>
                                                    @if ($worker->pivot->status == 'normal')
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
                                                    @endif
                                                </ul>
                                            </td>
                                            <td style="text-align: center">
                                                {{ $data->created_at->translatedFormat('j F Y - H:i:s') }}
                                            </td>
                                            <td style="text-align: center">@currency($worker->pivot->commission)    </td>
                                        </tr>
                                        @php
                                            $total_commission += $worker->pivot->commission
                                        @endphp
                                    
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" text-align="center"></td>
                                    <td class="table-primary" style="text-align: center">Total : </td>
                                    <td class="table-primary" style="text-align: center">@currency($total_commission)</td>
                                </tr>
                            </tfoot>
                        </table> --}}
                    </div>
                </div>      
            </div>
        </div>
    </div>
</div>



<script>

    // $(document).ready(function () {
        
    //     $("#print").click(function (e) { 
    //         e.preventDefault();
            
    //         var from_date = $("#from_date").val();
    //         var to_date = $("#to_date").val();
    //         var employee_id = $("#employee_id").val();

    //         console.log(employee_id);

    //         $.ajaxSetup({
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             }
    //         });

    //         $.ajax({
    //             type: "post",
    //             url: "/employee/detail-date",
    //             data: {
    //                 from_date: from_date,
    //                 to_date: to_date,
    //                 id: employee_id,    
    //             },
    //             dataType: "json",
    //             success: function (response) {

    //                 console.log(response);
    //                 $("#tbody").remove();
    //             }
    //         });
             
    //         console.log(from_date);
    //         console.log(to_date);

    //     });

    // });

</script>

@endsection