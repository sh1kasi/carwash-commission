@extends('layouts.admin')

@section('content')

<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <ul>
                                <li>{{ $error }}</li>
                            </ul>
                        @endforeach
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tambah Layanan</h5>
                        <form action="{{ route('product.post') }}" method=post>
                            @csrf
                            <div class="mb-3">
                              <label for="exampleInputEmail1" class="form-label">Nama Layanan</label>
                              <input type="text" id="service" value="" name="service" class="form-control" id="exampleInputEmail1" placeholder="Masukkan Nama Layanan" aria-describedby="emailHelp">
                            </div>
                            <div class="mb-3" id="layanan_kerja">
                                <label for="exampleInputPassword1" class="form-label">Layanan Kerja</label> <br>
                                <select class="form-select" name="work" id="type">
                                    <option value="" selected>Pilih Layanan Kerja</option>
                                    <option value="0">Biasa</option>
                                  <option value="1">Extra</option>
                                </select>
                            </div>
                            <div class="row pt-1 d-none" id="bundling-checkbox">
                                <label class="form-check-label mt-2" style="font-size: 15px" for="defaultCheck1">Layanan</label>
                                    @foreach ($product as $service)   
                                      <div class="form-group mt-1 col-md-4 mb-3">
                                        <input class="form-check-input cbservice " type="checkbox" name="servicesCheckbox" onclick="serviceBundle({{ $service->id }})" value="{{ $service->id }}" id="servicesCheckbox">
                                        <label class="form-check-label ps-2" style="font-size: 15px" for="defaultCheck1">{{ $service->service }} (@currency($service->price))</label>
                                      </div>
                                    @endforeach
                              </div>
                            <div class="mb-3" id="price">
                              <label for="exampleInputEmail1" class="form-label">Harga Layanan</label>
                              <input type="text" id="price" name="price" value="" class="form-control harga_layanan" placeholder="Masukkan Harga Layanan" aria-describedby="emailHelp">
                            </div>
                            <div class="mb-3" id="tipe_komisi">
                                <label for="exampleInputPassword1" class="form-label">Tipe Komisi</label> <br>
                                <select class="form-select" name="commission_type" id="commission_type">
                                    <option value="">Pilih Tipe Komisi</option>
                                    <option value="nominal">Nominal</option>
                                    <option value="persentase">Persentase</option>
                                </select>
                            </div>
                            <div class="mb-3" id="commission_value">
                              <label for="exampleInputEmail1" class="form-label">Komisi</label>
                              <input type="text" id="commission_value" name="commission_value" value="" class="form-control harga_layanan" placeholder="Masukkan Komisi Layanan" aria-describedby="emailHelp">
                            </div>
                            <button type="submit" id="submit" class="btn btn-primary">Submit</button>
                          </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="serviceArray" id="serviceArray">

<script>

        

    function serviceBundle(id) {
        console.log(id);
        
        

        console.log(serviceArray);
        
        $("#serviceArray").val(serviceArray);

    

        console.log($("#serviceArray").val());

         
    }


// $(document).ready(function () {


//     $("#submit").click(function (e) { 
//             e.preventDefault();
            
//             var type = $("#type_service").val();
//             var service = $("#service").val();
//             // var serviceArray = $("#serviceArray").val();

//             var serviceArray = [];
//         // var checked = $(".cbservice:checkbox:checked").val();
        
//         $(".cbservice:checkbox:checked").each(function () {
//             serviceArray.push($(this).val());
//         });

            

//             console.log(service);

//             $.ajaxSetup({
//                 headers: {
//                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                 }
//             });
            
//             $.ajax({
//               type: "post",
//               url: "/layanan/form/post",
//               data: {
//                 servicesCheckbox: serviceArray,
//                 type: type,
//                 name: service,
//               },
//               dataType: "json",
//               success: function (response) {

//                 location.href = '/layanan'
//                 // alert(response.message);

//               }
//             });
            
//             console.log('a');


//         });

    
    
//     $("#type_service").change(function (e) { 
//         e.preventDefault();
        
//         var type = $(this).val();

//         if (type == 'satuan') {
//             $("#bundling-checkbox").addClass('d-none');
//             $("#layanan_kerja").removeClass('d-none');
//             $("#tipe_komisi").removeClass('d-none');
//             $("#price").removeClass('d-none');
//             console.log('1 doang');
//         } else if (type == 'bundling'){
//             $("#bundling-checkbox").removeClass('d-none');
//             $("#layanan_kerja").addClass('d-none');
//             $("#tipe_komisi").addClass('d-none');
//             $("#price").addClass('d-none');
//         }

//     });

// });

</script>
    
@endsection