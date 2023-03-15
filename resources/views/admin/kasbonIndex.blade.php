@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Kasbon</h5>
                        {{-- <a href="/bundle/form" id="addBundle" class="btn btn-primary m-b-md">Tambah KasbonP</a> --}}
                        <div class="d-flex justify-content-evenly input-daterange">
                            <div class="from_date d-flex">                              
                                <p style="width: 155px">Dari tanggal: </p>
                                <input type="text" class="form-control mb-3" name="from" id="from_date">
                            </div>
                            <div class="to_date d-flex ms-2">
                                <p style="width: 250px">Hingga tanggal: </p>
                                <input type="text" class="form-control mb-3" name="to" value="" id="to_date">
                                <button class="btn btn-primary mb-3 ms-1" type="button" id="search_date"><i class="fa fa-search" aria-hidden="true"></i></i></button>
                                <button class="btn btn-warning mb-3 ms-1" type="button" id="refresh"><i class="fa fa-refresh" aria-hidden="true"></i></i></button>
                            </div>
                        </div>

                        {{-- <a href="#" id="addRow" data-bs-toggle="modal" data-bs-target="#transactionForm" class="btn btn-primary m-b-md">Tambah Transaksi</a> <br> --}}
                        {{-- <p style="font-size: 16px; font-weight: 600; text-align:center" class="pb-6">Transaksi Tanggal {{ $tgl }}</p> --}}
                        <table id="Tables123" class="display table table-bordered" style="width:100%; margin-top: 40px">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Pegawai</th>
                                    <th>Tanggal Pengangkatan</th>
                                    <th>Tanggal Terakhir Input Kasbon</th>
                                    <th>Sisa Nominal</th>
                                    <th>Detail</th>
                                    {{-- <th>Detail Komisi</th> --}}
                                </tr>
                            </thead>
                            <tbody id="indexTable">
                            </tbody>
                        </table>
                    </div>
                </div>      
            </div>
        </div>
    </div>
</div>

@include('admin.adminModal.kasbonInput')
@include('admin.adminModal.kasbonDetail')

<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

@if (session()->has('kasbon_empty'))
    <script>
        toastr.error("{!! Session('kasbon_empty') !!}");
    </script>
@endif
@if (session()->has('success'))
    <script>
        toastr.success("{!! Session('success') !!}");
    </script>
@endif

@if (session()->has('error'))
    <script>
        toastr.error("{!! Session('error') !!}");
    </script>
@endif
    
<script>

    function inputKasbon(id) { 
        // console.log(id); 
        $("#employee_id").val(id);
    }

    $(document).ready(function () {
        
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        // load_data();

        
        // function load_data(from_date = '', to_date = '') {
            
            $('#Tables123').DataTable({
                processing: true,
                serverSide: true,
                filter: true,
                searching: false,
                
                ajax: {
                type: 'GET',
                url: '/kasbon/json',
                // data: {
                // from_date: from_date,
                // to_date: to_date,
                // }
              },
              columns: [
                  {data: 'DT_RowIndex', name: '#'},
                  {data: 'name', name: 'Nama Pegawai'},
                  {data: 'promoted_date', name: 'Tanggal Pengangkatan'},
                  {data: 'kasbon_input', name: 'Tanggal Input Kasbon'},
                  {data: 'sisa_nominal', name: 'Sisa Nominal'},
                  {data: 'detail', name: 'Detail'},
                ]
            });
            
        // }    
        
    });

</script>

@endsection