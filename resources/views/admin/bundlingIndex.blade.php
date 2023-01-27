@extends('layouts.admin')

@section('content')
    
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />


<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Bundling</h5>
                        <a href="/bundle/form" id="addBundle" class="btn btn-primary m-b-md">Tambah Bundling</a>
                        <table id="Tables123" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="text-align: center">Nama Bundle</th>
                                    <th style="text-align: center">Layanan</th>
                                    <th style="text-align: center">Harga Bundling</th>
                                    <th style="text-align: center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                              @foreach ($bundle as $data)
                                  <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>
                                        @foreach ($data->products as $layanan)
                                            <ul>
                                                <li>
                                                    {{ $layanan->service }}
                                                </li>
                                            </ul>
                                        @endforeach
                                    </td>
                                    <td>@currency($data->total_price)</td>
                                    <td style="text-align: center">
                                        <a href="/bundle/edit/{{ $data->id }}"><i class="fa fa-edit fa-xl" aria-hidden="true"></i></a> &nbsp; &nbsp;
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

<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

@if (session()->has('success'))
<script>
  toastr.success("{!! Session('success') !!}");
</script>
@endif

<script>
    $(document).ready( function () {
        $('#Tables123').DataTable({
            paging: false,
        });

        $("a#delete").click(function (e) { 
            e.preventDefault();
            
            var id = $(this).data('id');
            // console.log(id);
            var name = $(this).data('name');
            swal({
               title: "Kamu yakin?",
               text: "Bundle " +name+" akan terhapus!",
               icon: "warning",
               buttons: true,
               dangerMode: true,
               })
               .then((willDelete) => {
               if (willDelete) {
                   window.location = "/bundle/delete/"+id+""
                   swal("Bundle "+name+" berhasil terhapus" , {
                   icon: "success",
                   buttons: false,
                   });
                   
               }
           });

        });

    } );
</script>

@endsection