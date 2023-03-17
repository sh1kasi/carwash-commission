
@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    td {
        align-content: center;
    }
</style>


{{-- @dd($bundleArray); --}}
<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Transaksi</h5>

                        <div class="d-flex justify-content-evenly input-daterange">
                            <div class="from_date d-flex">                              
                                <p style="width: 155px">Dari tanggal: </p>
                                <input type="text" class="form-control mb-3" name="from" value="" id="from_date" style="border-radius: 10px !important">
                            </div>
                            <div class="to_date d-flex ms-2">
                                <p style="width: 250px">Hingga tanggal: </p>
                                <input type="text" class="form-control mb-3" name="to" value="" id="to_date">
                                <button class="btn btn-primary mb-3 ms-1" type="button" id="search_date"><i class="fa fa-search" aria-hidden="true"></i></i></button>
                                <button class="btn btn-warning mb-3 ms-1" type="button" id="refresh"><i class="fa fa-refresh" aria-hidden="true"></i></i></button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="total_transaction">
                                    Total Transaksi
                                </label>
                                <input type="text" id="total_transaction" class="form-control" readonly>
                            </div>
    
                            <div class="form-group col-md-6 pb-4">
                                <label for="total_commision">
                                    Total Komisi
                                </label>
                                <input type="text" id="total_commision" class="form-control" readonly>
                            </div>
                        </div>

                        <a href="#" id="addRow" data-bs-toggle="modal" data-bs-target="#transactionForm" class="btn btn-primary m-b-md">Tambah Transaksi</a> <br>
                        {{-- <p style="font-size: 16px; font-weight: 600; text-align:center" class="pb-6">Transaksi Tanggal {{ $tgl }}</p> --}}
                        <table id="Tables123" class="display table table-bordered" style="margin-top: 40px">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="width: 200px">NO POL</th>
                                    <th>Jenis Layanan</th>
                                    <th>Penggarap</th>
                                    <th style="width: 150px">Total Harga</th>
                                    <th>Tanggal</th>
                                    <th>Detail Komisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="indexTable">
                              {{--  @foreach ($transaction as $data)    
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><b>{{ $data->customer }}</b></td>
                                        <td>
                                            @foreach ($data->products as $item)
                                            <ul>
                                                <li>{{ $item->service }}</li>    
                                            </ul>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($data->employees as $item)
                                            <ul>
                                                <li>{{ $item->name }}</li>    
                                            </ul>
                                            @endforeach
                                        </td>
                                        <td>@currency($data->total_price)</td>
                                        <td>{{ $data->created_at->timezone("Asia/Jakarta") }}</td>
                                        <td>
                                            <button class="btn btn-primary" id="commissionDetail" onclick="commissionDetail({{ $data->id }})" data-bs-toggle="modal" data-bs-target="#CommissionModal">Detail Komisi</button>
                                        </td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>      
            </div>
        </div>
    </div>
</div>

@include('admin.adminModal.transactionForm')
@include('admin.adminModal.commissionDetail')
@include('admin.adminModal.extraWorksmodal')


<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

@php
    $tambahan = session()->get('tambahan');
    // dd($tambahan);
@endphp

@if (!empty(Request('tanggal')))
    <input type="hidden" id="tanggal" value="{{ Request('tanggal') }}">
    <input type="hidden" id="nopol" value="{{ Request('nopol') }}">
    <input type="hidden" id="transaksi_id" value="{{ Request('transaksi_id') }}" name="request_transaksi_id">
    <script>
        $(document).ready(function () {
            $("#transactionForm").modal('show')
            $("#date").val($("#tanggal").val());
            $(".modal-body").append(`<input type="hidden" id="transaksi_id" value="${transaksi_id}">`);
            $("#select2-nopol-container").attr('title', $("#nopol").val());
            
            // $(".select2-search__field").val($("#nopol").val());
        });
    </script>
@endif

@if (Session::get('id'))
    {{-- @dd(Session('id')) --}}
    <script>

        $(document).ready(function () {
            
        $('#CommissionModal').modal('show');



        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

    $.ajax({
        type: "post",
        url: "transaction-detail",
        data: {
            id: {!! session('id') !!}
        },
        dataType: "json",
        success: function (response) {

            // console.log($("#transaction_id").val());
            $("#tgl_transaksi").html(`Tanggal Transaksi: &nbsp; ${response.tanggal_transaksi}`);
            $("#plat_nomor").html(`NOPOL: &nbsp; ${response.transaction.customer}`);
            $("#service").html(`Layanan: <ul id="buyservice"></ul>`);
            $(response.grouped_product).each(function (key, grouped) {
              // console.log(buy_service);
                $("#buyservice").append(`<li><b>${grouped.employee_products.service} &nbsp; &nbsp; (Rp ${grouped.employee_products.price.toLocaleString("id-ID")})</b></li>`);
                // var service = buy_service.service;
            });
            $("#totayl_harga").html(`Total: &nbsp; Rp ${response.transaction.total_price.toLocaleString("id-ID")}`);
            $("#penggarap").html("");

            console.log(service);

            $(response.worker).each(function (key, workers) {

                var append = `<tr><td>${workers.worker}</td><td><ul>`
                var services = ''
                // var commission = ''
                var total_commission = 0
                $(workers.services).each(function (key, product) {
                    total_commission += product.commission
                    services = services +
                        `<li>${product.employee_products.service} (Rp ${product.commission.toLocaleString('id-ID')})</li>`;
                    // console.log(`<li>${product.employee_products.service}</li>`);
                    // commission = commission + ``;
                });
                append = append + services + '</ul></td><td>' + 'Rp ' + total_commission
                    .toLocaleString('id-ID') + '</td></tr>'
                
                $("#penggarap").append(append);
                $("#total_komisi").html(
                `Rp ${response.transaction.comission.toLocaleString('id-ID')}`);
            
            });

            
        }
    });


        });

    </script>
@endif

@if ($tambahan == true)
    <script>

        $(document).ready(function () {

            var extra_product = {!! session('extra_product') !!}
            var worker = {!! session('worker') !!}
            var id = {!! session('transaction_id') !!}


            $('#CommissionModal').modal('hide');
            $('#extraWorksModal').modal('show');
            var html = ''
            var extraArray = [];
            $("#extra_transaction_id").val(id);
            $(extra_product).each(function (index, ex_product) {
              // console.log(ex_product);
              extraArray.push(ex_product.id);
              console.log(extraArray);
              html += `
                    <h5 class="modal-title" id="extraWorksModalLabel">${ex_product.service}</h5>
                    <input type="hidden" id="product" name="product_id[]" value="${ex_product.id}">
              `
              $(worker).each(function (key, extra) {
                console.log(extra.name);
                html += `
                  <input type="checkbox" name="employee_id_${index}[]" class="cbextra ms-2" onclick="extraWorkers(${extra.id} ,${ex_product.id})" value="${extra.id}" id="inputExtra">
                  <label id="extraName">${extra.name}</label>  
                  `
                  $("#extraArray").val(extraArray);                
                });
                });
              $("#pekerjaExtra").append(html);

            
            // $(response.extra_product).each(function (key, extra) {
              // $("#extraWorksModalLabel").html(`Pilih penggarap ${response.extra_product}`);
            // });
            
            $('#transactionForm').modal('hide');
            
        });

    </script>
@endif


<input type="hidden" name="tambahan" id="tambahan" value>





<script>

    
    $(document).ready( function () {
        
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        load_data();

        $("#search_date").click(function (e) { 
            e.preventDefault();
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();
            if (from_date != '' && to_date != '') {
                $("#Tables123").DataTable().destroy();
                load_data(from_date, to_date);
            } else {
                 toastr.error("Harap isi kedua tanggal tanggal tersebut!");
            }
        });

        $("#refresh").click(function (e) { 
            e.preventDefault();
            $("#from_date").val('');
            $("#to_date").val('');
            $("#Tables123").DataTable().destroy();
            load_data();
        });

        $('.input-daterange').datepicker({
            todayBtn: 'linked',
            format: 'yyyy-mm-dd',
            autoclose: true,
        });

        function load_data(from_date = '', to_date = '') {
            
            $('#Tables123').DataTable({
              processing: true,
              serverSide: true,
              filter: true,
              paging: false,
              searching: false,

              ajax: {
                  type: 'GET',
                url: '/transaction/json',
                data: {
                    from_date: from_date,
                    to_date: to_date,
                },
                dataSrc: function(res){
                    $('#total_commision').val(numeral(res.total_komisi).format("0,0"))
                    $('#total_transaction').val(numeral(res.total_transaksi).format("0,0"))

                return res.data
                }
              },
              columns: [
                  {data: 'id', name: '#'},
                  {data: 'customer', name: 'NO POL'},
                  {data: 'service', name: 'Jenis Layanan'},
                  {data: 'workers', name: 'Penggarap'},
                  {data: 'total_price', name: 'Total Harga'},
                  {data: 'tanggal', name: 'Tanggal'},
                  {data: 'detail', name: 'Detail Komisi'},
                  {data: 'aksi', name: 'Aksi'},
                  
              ]
            });

        }






    });


</script>


@endsection
