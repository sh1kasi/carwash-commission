@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pegawai</h5>
                        <a href="/employee/form" id="addEmployee" class="btn btn-primary m-b-md">Tambah Pegawai</a>
                        <table id="Tables123" class="display table table-bordered" style="width:100%" align="center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    {{-- <th>Status</th> --}}
                                    <th>Total Mengerjakan</th>
                                    <th>Detail</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employee as $data)    
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->name }}</td>
                                        {{-- <td>{{ $data->role }}</td> --}}
                                        <td>{{ $transaction_employee->where('employee_id', $data->id)->count() }}</td>
                                        @php
                                           $count = $transaction_employee->where('employee_id', $data->id)->count();
                                        @endphp
                                        {{-- <input type="hidden" id="WorkedCount" value="{{ $transaction_employee->where('employee_id', $data->id)->count() }}"> --}}
                                        <td align="center"><a href="/employee/detail/{{ $data->id }}" id="detail"
                                            @if ($count > 0)
                                                class="btn btn-success"
                                            @else 
                                                class="btn btn-primary disabled"
                                            @endif>Detail</a>
                                        </td>
                                        <td style="text-align: center">
                                            <a href="/employee/edit/{{ $data->id }}"><i class="fas fa-edit fa-xl"></i></a> &nbsp; &nbsp;
                                            <a href="#" id="delete" data-id="{{ $data->id }}" data-name="{{ $data->name }}"><i class="fa fa-trash text-danger fa-xl" aria-hidden="true"></i></a>
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

@if (count($trashed_employee) != 0)
<div class="page-content" style="1500px">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pegawai (Yang terhapus)</h5>
                        {{-- <a href="/employee/form" id="addEmployee" class="btn btn-primary m-b-md">Tambah Pegawai</a> --}}
                        <table id="Tables123" class="display table table-bordered" style="width:100%" align="center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    {{-- <th>Status</th> --}}
                                    <th>Total Mengerjakan</th>
                                    <th>Detail</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trashed_employee as $data)    
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->name }} (Terhapus)</td>
                                        {{-- <td>{{ $data->role }} </td> --}}
                                        <td>{{ $transaction_employee->where('employee_id', $data->id)->count() }}</td>
                                        @php
                                           $count = $transaction_employee->where('employee_id', $data->id)->count();
                                        @endphp
                                        {{-- <input type="hidden" id="WorkedCount" value="{{ $transaction_employee->where('employee_id', $data->id)->count() }}"> --}}
                                        <td align="center"><a href="/employee/detail/{{ $data->id }}" id="detail"
                                            @if ($count > 0)
                                                class="btn btn-success"
                                            @else 
                                                class="btn btn-primary disabled"
                                            @endif>Detail</a>
                                        </td>
                                        <td style="text-align: center">
                                            <a href="/employee/restore/{{ $data->id }}" type="button" class="btn btn-primary">Restore</i></a> &nbsp; &nbsp;
                                            {{-- <a href="#" id="delete" data-id="{{ $data->id }}" data-name="{{ $data->name }}"><i class="fa fa-trash text-danger fa-xl" aria-hidden="true"></i></a> --}}
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
@endif

<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

@if (session()->has('success'))
<script>
  toastr.success("{!! Session('success') !!}");
</script>
@elseif (session()->has('failed'))
<script>
    toastr.error("{!! Session('failed') !!}");
</script>
@endif

<script>
    $(document).ready( function () {
        $('#Tables123').DataTable({"paging": false});
    } );
</script>

<script>

    $(document).ready(function () {
        
        $("a#delete").click(function (e) { 
            e.preventDefault();

            // alert('a');
            
            var id = $(this).data('id');
            // console.log(id);
            var name = $(this).data('name');
            swal({
               title: "Kamu yakin?",
               text: "Pegawai yang bernama " +name+" akan terhapus!",
               icon: "warning",
               buttons: true,
               dangerMode: true,
               })
               .then((willDelete) => {
               if (willDelete) {
                   window.location = "/employee/delete/"+id+""
                   swal("Pegawai bernama "+name+" berhasil terhapus" , {
                   icon: "success",
                   buttons: false,
                   });
                   
               }
           });

        });

    });

</script>
    
@endsection