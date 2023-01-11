@extends('layouts.admin')

@section('content')

<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Layanan</h5>
                        {{-- <a href="#" id="addRow" data-bs-toggle="modal" data-bs-target="#transactionForm" class="btn btn-primary m-b-md">Tambah Transaksi</a> --}}
                        <table id="Tables123" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Layanan</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($service as $data)    
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->service }}</td>
                                        <td>@currency($data->price)</td>
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

<script>
    $(document).ready( function () {
        $('#Tables123').DataTable();
    } );
</script>
    
@endsection