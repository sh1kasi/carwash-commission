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
                        <h5 class="card-title">Edit Transaksi</h5>
                        <form action="/transaction/update/{{ $transaction->id }}" method=post>
                            @csrf
                            {{-- <div class="form-group pb-1">
                                <label for="exampleInputEmail1"><b>NO PLAT KENDARAAN</b></label> <br>
                                <select style="width: 100%;" class="form-coppntrol mt-2 mb-2" id="nopol" style="text-transform: uppercase;" aria-describedby="emailHelp" name="nopol"></select>
                            </div> --}}
                            <div class="form-group pb-1 input-daterange">
                              <label for="exampleInputEmail1"><b>Tanggal</b></label> <br>
                              <input style="width: 25%;" value="{{ $transaction->created_at->format('Y-m-d') }}" class="form-control mt-2 mb-2" id="date" style="text-transform: uppercase;" aria-describedby="emailHelp" name="date" />
                            </div>
                            <div class="form-group pb-1">
                                <label for="exampleInputEmail1"><b>NO PLAT KENDARAAN</b></label> <br>
                                <input type="text" style="width: 100%;" value="{{ $transaction->customer }}" class="form-control mt-2 mb-2" id="nopol" style="text-transform: uppercase;" aria-describedby="emailHelp" name="nopol" />
                            </div>
                              
                              <div class="row pt-1">
                                <label class="form-check-label mt-2" style="font-size: 15px" for="defaultCheck1"><b>LAYANAN</b></label>
                                @foreach ($product as $service)   
                                  <div class="form-group mt-1 col-md-6">
                                    <input class="form-check-input cbservice" 
                                    @if (in_array($service->id, $productArray))
                                        {{ 'checked' }}
                                    @endif
                                    data-commission="{{ $service->commission_value }}" data-type="{{ $service->type_commission }}" type="checkbox" name="service[]" onclick="services({{ $service->id }})" value="{{ $service->id }}" id="servicesCheckbox">
                                    <label class="form-check-label ps-2" style="font-size: 15px" for="defaultCheck1">{{ $service->service }} (@currency($service->price))</label>
                                  </div>
                                @endforeach
                                <label class="form-check-label mt-3" style="font-size: 15px" for="defaultCheck1"><b>LAYANAN BUNDLING</b></label>
                            
                                @php
                                    $bundling_price = 0;
                                    $product_array = [];
                                    $bundleVal = 0;
                                @endphp
                                @foreach ($bundle as $bundling)
                                <div class="form-group col-md-6">
                                  <input class="form-check-input cbbundling" type="checkbox" name="bundlingsCheckbox[]" onclick="services({{ $bundling->id }})" value="{{ $bundling->id }}" id="bundlingsCheckbox">
                                  <label class="form-check-label ps-2" style="font-size: 15px" for="defaultCheck1">{{ $bundling->name }} (@currency($bundling->total_price))
                                    @foreach ($bundling->products as $item)
                                    {{-- @dd($item); --}}
                                    <ul style="margin-bottom: 0px !important">
                                      {{-- <li>{{ $item->id }}</li> --}}
                                      <input type="hidden" id="product_id" value="{{ $item->id }}">
                                      <li>{{ $item->service }}</li>
                                    </ul>
                                    @endforeach
                                        </label>
                                      </div>
                                      {{-- <input type="hidden" id="bundleArray" name="bundlingArray[]" value="{!! json_encode($bundleArray) !!}"> --}}
                                @endforeach
                              </div>
                            
                              <div class="row pt-2 pb-3" style="border-bottom: 1px solid #c5bebefa"> 
                                <label class="form-check-label mt-3" style="font-size: 15px" for="defaultCheck1"><b>PENGGARAP</b></label>
                                @foreach ($employees as $employee)   
                                  <div class="form-group mt-1 col-md-6">
                                    <input class="form-check-input cbemployee"
                                    @if (in_array($employee->id, $employeeArray))
                                        {{ 'checked' }}
                                    @endif
                                    name="employee[]" type="checkbox" value="{{ $employee->id }}" id="employee">
                                    <label class="form-check-label ps-2" style="font-size: 15px" for="defaultCheck1">{{ $employee->name }}</label>
                                  </div>
                                @endforeach
                              </div>
                              <div class="d-flex justify-content-between pt-2">
                                <div class="total"><b>Total Harga: </b></div>
                                <div id="amount" class="text-danger">@currency($transaction->total_price)</div>
                            </div>
                            <input type="hidden" name="total_price" id="total_price" value="{{ $transaction->total_price }}">
                            <input type="hidden" name="employee_detach" value="{{ $employee_value }}">
                            <input type="hidden" name="product_detach" value="{{ $product_value }}">
                            <button type="submit" id="submit" class="btn btn-primary mt-3">Submit</button>
                          </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="old_nopol" id="old_nopol" value="{{ $transaction->customer }}">

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>

// var selectOption = {
//         placeholder: $("#old_nopol").val(),
//         minimumInputLength: 3,
//         allowClear: true,
//         tags:true,
//         theme: "bootstrap-5",
//         value: 'sss',
//         ajax: {
//             url:  "/transaction-select",
//             dataType: "json",
//             delay: 250,
//             type: "POST",
//             headers: {
//                 'X-CSRF-TOKEN': token,
//             },
//             data: function (params) {
//                 return {
//                     search: params.term,
//                     // branch_id: $('#branch_id').val(),
//                 };
//             },
//             processResults: function (data) {
//                 var select2Data = $.map(data, function (obj) {
//                     obj.id = obj.customer;
//                     obj.text = `${obj.customer}`;
//                     return obj;
//                 });
//                 return {
//                     results: select2Data,
//                 };
//             },
//             cache: true,
//         },
//     };

//     $('#nopol').select2(selectOption);

    // $(document).ready(function () {
    //     $(".select2-search__field")[0].val($("#old_nopol").val());
    // });

    $(document).ready(function () {
      
      $(".input-daterange").datepicker({
        todayBtn: 'linked',
        format: 'yyyy-mm-dd',
        autoclose: true, 
      });

    });

       
    console.log($("#nopol").data("select2").dropdown.$search.val($("#old_nopol").val()));

  function services(id) {


    var serviceArray = [];
    $(".cbservice:checkbox:checked").each(function () {
      serviceArray.push($(this).val());
    });


    if ($(".cbbundling:checkbox").is(':checked')) {
      console.log('not checked');
        var bundleArray = [];
        $(".cbbundling:checkbox:checked").each(function () {
          bundleArray.push($(this).val());
        });
        // var bundleVal = $(".cbbundling:checkbox:checked").val();
        // var bundleArray = bundleVal.split(',')
        // console.log(bundleArray);
      } else {
        console.log('checked');
        var bundleArray = [];
        // console.log(bundleArray);
      }

      console.log(bundleArray);



      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
        type: "post",
        url: "/transaction-total",
        data: {
          serviceArray: serviceArray,
          bundling: bundleArray,
        },
        dataType: "json",
        success: function (response) {
          $("#amount").html(`Rp ${response.total_price.toLocaleString("id-ID")}`);
          $("#total_price").val(response.total_price);
          // if (serviceArray.length > 0) {
          // } else {
          //   $("#amount").html(`Rp 0`);
          // }
        }
      });
  } 


</script>

    
@endsection