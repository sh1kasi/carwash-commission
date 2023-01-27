@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />


<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Layanan</h5>
                        <a href="/layanan/form" id="addService" class="btn btn-primary m-b-md">Tambah Layanan</a>
                        <table id="Tables123" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Layanan</th>
                                    <th>Harga</th>
                                    <th>Tipe Komisi</th>
                                    <th colspan="2" style="text-align: center">Komisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($service as $data)    
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->service }}</td>
                                        <td>@currency($data->price)</td>
                                        <td>{{ $data->type_commission }}</td>
                                        {{-- <td rowspan="1">
                                            @if ($data->type_commission == 'persentase')
                                                {{ $data->commission_value }}%
                                            @else
                                                @currency($data->commission_value)
                                            @endif
                                        </td> --}}
                                        <td>
                                            @if ($data->type_commission == 'persentase')
                                                {{ $data->commission_value }}%
                                            @else
                                                {{ $data->commission_value / $data->price * 100 }}%
                                            @endif
                                        </td>
                                        <td>
                                            @if ($data->type_commission == 'persentase')
                                                @currency($data->commission_value / 100 * $data->price)
                                            @else
                                                @currency($data->commission_value)
                                            @endif
                                        </td>
                                        <td style="text-align: center">
                                            <a href="/layanan/edit/{{ $data->id }}"><i class="fas fa-edit fa-xl"></i></a> &nbsp; &nbsp;
                                            <a href="#" id="delete" data-id="{{ $data->id }}" data-name="{{ $data->service }}"><i class="fa fa-trash text-danger fa-xl" aria-hidden="true"></i></a>
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

@if (count($trashed_service) != 0)
<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Layanan (Yang terhapus)</h5>
                        <a href="/layanan/form" id="addService" class="btn btn-primary m-b-md">Tambah Layanan</a>
                        <table id="Tables123" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Layanan</th>
                                    <th>Harga</th>
                                    <th>Tipe Komisi</th>
                                    <th colspan="2" style="text-align: center">Komisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trashed_service as $data)    
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->service }}</td>
                                        <td>@currency($data->price)</td>
                                        <td>{{ $data->type_commission }}</td>
                                        {{-- <td rowspan="1">
                                            @if ($data->type_commission == 'persentase')
                                                {{ $data->commission_value }}%
                                            @else
                                                @currency($data->commission_value)
                                            @endif
                                        </td> --}}
                                        <td>
                                            @if ($data->type_commission == 'persentase')
                                                {{ $data->commission_value }}%
                                            @else
                                                {{ $data->commission_value / $data->price * 100 }}%
                                            @endif
                                        </td>
                                        <td>
                                            @if ($data->type_commission == 'persentase')
                                                @currency($data->commission_value / 100 * $data->price)
                                            @else
                                                @currency($data->commission_value)
                                            @endif
                                        </td>
                                        <td style="text-align: center">
                                            <a href="/layanan/restore/{{ $data->id }}" type="button" class="btn btn-primary">Restore</i></a> &nbsp; &nbsp;
                                            {{-- <a href="#" id="delete" data-id="{{ $data->id }}" data-name="{{ $data->service }}"><i class="fa fa-trash text-danger fa-xl" aria-hidden="true"></i></a> --}}
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
        $('#Tables123').DataTable();
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
               text: "Layanan " +name+" akan terhapus!",
               icon: "warning",
               buttons: true,
               dangerMode: true,
               })
               .then((willDelete) => {
               if (willDelete) {
                   window.location = "/layanan/delete/"+id+""
                   swal("Layanan "+name+" berhasil terhapus" , {
                   icon: "success",
                   buttons: false,
                   });                   
               }
           });

        });

    });

</script>
    
@endsection