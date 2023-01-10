@extends('layouts.admin')

@section('content')

<style>
    td {
        align-content: center;
    }
</style>

<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Transaksi</h5>
                        <a href="#" id="addRow" data-bs-toggle="modal" data-bs-target="#transactionForm" class="btn btn-primary m-b-md">Tambah Transaksi</a>
                        <table id="Tables123" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NO POL</th>
                                    <th>Jenis Layanan</th>
                                    <th>Penggarap</th>
                                    <th>Total Harga</th>
                                    <th>Tanggal</th>
                                    <th>Detail Komisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction as $data)    
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
                                @endforeach
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


<script>
    $(document).ready( function () {
      $('#Tables123').DataTable();
    });


</script>


@endsection
