@extends('layouts.admin')

@section('content')

<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pegawai</h5>
                        {{-- <a href="#" id="addRow" data-bs-toggle="modal" data-bs-target="#transactionForm" class="btn btn-primary m-b-md">Tambah Transaksi</a> --}}
                        <table id="Tables123" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Total Mengerjakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employee as $data)    
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $transaction_employee->where('employee_id', $data->id)->count() }}</td>
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