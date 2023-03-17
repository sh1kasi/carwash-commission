<!-- Modal -->

{{-- @dd(count($bundle)) --}}

<div class="modal fade" id="transactionForm" role="dialog" aria-labelledby="transactionFormLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 700px" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transactionFormLabel">TAMBAH TRANSAKSI</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"></span>
        </button>
      </div>
      <div class="modal-body">

        <ul id="err_list"></ul>

        <div class="form-group pb-1 input-daterange">
          <label for="exampleInputEmail1"><b>Tanggal</b></label> <br>
          <input style="width: 45%;" value="{{ $currentDate }}" class="form-control mt-2 mb-2" id="date" style="text-transform: uppercase;" aria-describedby="emailHelp" name="date" />
        </div>
        <div class="form-group pb-1">
          <label for="exampleInputEmail1"><b>NO PLAT KENDARAAN</b></label> <br>
          <select style="width: 100%;" class="form-control mt-2 mb-2" id="nopol" style="text-transform: uppercase;" aria-describedby="emailHelp" name="nopol">
            @if (Request('nopol'))
                <option value="{{ Request('nopol') }}">{{ Request('nopol') }}</option>
            @endif
          </select>
          {{-- < name="nopol" class="form-control mt-1 mb-2" id="nopol" aria-describedby="emailHelp" placeholder="Masukkan nomor plat kendaraan"> --}}
        </div>
        
        <div class="row pt-1">
          <label class="form-check-label mt-2" style="font-size: 15px" for="defaultCheck1"><b>LAYANAN</b></label>
          @foreach ($product as $service)   
            <div class="form-group mt-1 col-md-6">
              <input class="form-check-input cbservice" data-commission="{{ $service->commission_value }}" data-type="{{ $service->type_commission }}" type="checkbox" name="servicesCheckbox" onclick="servicesCheckbox({{ $service->id }})" value="{{ $service->id }}" id="servicesCheckbox">
              <label class="form-check-label ps-2" style="font-size: 15px" for="defaultCheck1">{{ $service->service }} (@currency($service->price))</label>
            </div>
          @endforeach
          @if (count($bundle) != 0)
              {{-- <h5 class="mt-2">Tidak ada data</h5> --}}
              <label class="form-check-label mt-3" style="font-size: 15px" for="defaultCheck1"><b>LAYANAN BUNDLING</b></label>
              {{-- <h6>Tidak ada data!</h6> --}}
              @php
                  $bundling_price = 0;
                  $product_array = [];
                  $bundleVal = 0;
              @endphp
                  @foreach ($bundle as $bundling)
                  <div class="form-group col-md-6">
                    <input class="form-check-input cbbundling" type="checkbox" name="bundlingsCheckbox" onclick="servicesCheckbox({{ $bundling->id }})" value="{{ $bundling->id }}" id="bundlingsCheckbox">
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
          @endif
        </div>

        <div class="row pt-2 pb-3" style="border-bottom: 1px solid #c5bebefa"> 
          <label class="form-check-label mt-3" style="font-size: 15px" for="defaultCheck1"><b>PENGGARAP</b></label>
          @foreach ($employees as $employee)   
            <div class="form-group mt-1 col-md-6">
              <input class="form-check-input cbemployee" name="employee" type="checkbox" value="{{ $employee->id }}" id="employee">
              <label class="form-check-label ps-2" style="font-size: 15px" for="defaultCheck1">{{ $employee->name }}</label>
            </div>
          @endforeach
        </div>
        <div class="d-flex justify-content-between pt-2">
          <div class="total"><b>Total Harga: </b></div>
          <div id="amount" class="text-danger">@currency(0)</div>
          
        </div>

      </div>
      <div class="modal-footer">
        <button type="submit" id="submit" class="btn btn-primary" >Simpan Transaksi</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<input type="hidden" value="" id="total_price">
<input type="hidden" value="" id="transaction_id">

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>


  // console.log($(".cbbundling").val());

var selectOption = {
        placeholder: "Pilih Plat Nomor",
        minimumInputLength: 1,
        allowClear: true,
        tags:true,
        theme: "bootstrap-5",
        ajax: {
            url:  "/transaction-select",
            dataType: "json",
            delay: 250,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': token,
            },
            data: function (params) {
                return {
                    search: params.term,
                    // branch_id: $('#branch_id').val(),
                };
            },
            processResults: function (data) {
                var select2Data = $.map(data, function (obj) {
                    obj.id = obj.customer;
                    obj.text = `${obj.customer}`;
                    return obj;
                });
                return {
                    results: select2Data,
                };
            },
            cache: true,
        },
    };

    $('#nopol').select2(selectOption);



  function bundlingsCheckbox(id) {
    // var bundlingArray = [];
    // $(".cbbundling:checkbox:checked").each(function () {
    //   // bundlingArray.push($(this).val());
      
    // });

      

      if ($(".cbbundling:checkbox").is(':checked')) {
        var bundleVal = $(".cbbundling:checkbox:checked").val();
        var bundleArray = bundleVal.split(',')
        console.log(bundleVal);
      } else {
        var bundleArray = [];
        // console.log(bundleArray);
      }

      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
        type: "post",
        url: "/transaction-total",
        data: {
          // serviceArray: serviceArray,
          bundling: bundleArray,
        },
        dataType: "json",
        success: function (response) {
          if (bundleArray.length > 0) {
            $("#amount").html(`Rp ${response.total_price.toLocaleString("id-ID")}`);
            $("#total_price").val(response.total_price);
          } else {
            $("#amount").html(`Rp 0`);
          }
        }
      });

    // var bundleVal = $(".cbbundling:checkbox:checked").val();
    // var bundleArray = bundleVal.split(',')


  }

  function servicesCheckbox(id) {

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

  $(document).ready(function () {
    $("#submit").click(function (e) { 
      e.preventDefault();

      var nopol = $("#nopol").val();
      var service = $("#servicesCheckbox").val();
      var employee = $("#employee").val();
      var date = $("#date").val();
      var total_price = $("#total_price").val();
      var commission = $("#servicesCheckbox").data('commission');
      var commiss_check = $("#servicesCheckbox").data('type');
      var request_transaksi_id = $('input[name="request_transaksi_id"]').val()

      console.log(commiss_check);

      var serviceArray = [];
      var employeeArray = [];

      // window.location.href('/transaksi')

      
    $(".cbservice:checkbox:checked").each(function () {
      serviceArray.push($(this).val());
    });

    $(".cbemployee:checkbox:checked").each(function () {
      employeeArray.push($(this).val());
    });
    
    if ($(".cbbundling:checkbox").is(':checked')) {
      var bundleArray = [];
        $(".cbbundling:checkbox:checked").each(function () {
          bundleArray.push($(this).val());
        });  
      // var bundleVal = $(".cbbundling:checkbox:checked").val();
        // var bundleArray = bundleVal.split(',')
        // // console.log(bundleArray);
      } else {
        var bundleArray = [];
        // console.log(bundleArray);
      };

      // console.log(serviceArray);
      // return false;

      if (bundleArray.length < 1 && serviceArray.length < 1) {
        $("#err_list").html("");
        $("#err_list").addClass("alert alert-danger");
        $("#err_list").append(`<li style="margin-left: 15px;">Select at least one of the service</li>`);
      } else {

      

  

      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $.ajax({
        type: "post",
        url: "/transaction-store",
        data: {
          nopol: nopol,
          service: serviceArray,
          employee: employeeArray,
          total_price: total_price,
          date: date,
          bundling: bundleArray,
          commiss_check: commiss_check,
          request_transaksi_id: request_transaksi_id,
        },
        dataType: "json",
        success: function (response) {

          // console.log(response);

          if (response.status == 400) {
            // console.log(bundleArray);
            $("#err_list").html("");
            if (bundleArray.length == 0 && serviceArray.length == 0) {
              $("#err_list").append(`<li style="margin-left: 15px;">Select at least one of the service</li>`);
            }
            $("#err_list").addClass('alert alert-danger');
            $("#err_list").append(`<li style="margin-left: 15px;">${response.errors.nopol[0]}</li>`);
            $("#err_list").append(`<li style="margin-left: 15px;">${response.errors.employee[0]}</li>`);
            // $(response.errors).each(function (key, err_values) {
            //   console.log(err_values);
            // });
          } else {
                 
              if (response.tambahan == true) {
                // window.location.reload();
                $("#transaction_id").val(response.data.id);
                $("#extra_transaction_id").val(response.data.id);
                // $(response.worker).each(function (key, extra) {
                //   $("#extraWorks").append(`
                  
                //   <input type="checkbox" name="inputExtra" class="cbextra ms-2" onclick="extraWorkers(${extra.id})" value="${extra.id}" id="inputExtra">
                //   <label for="inputExtra" id="extraName">${extra.name}</label>  
                //   `);
                // });
                $('#extraWorksModal').modal('show');
                var html = ''
                var extraArray = [];
                $(response.extra_product).each(function (index, ex_product) {
                  // console.log(ex_product);
                  extraArray.push(ex_product.id);
                  console.log(extraArray);
                  html += `
                        <h5 class="modal-title" id="extraWorksModalLabel">${ex_product.service}</h5>
                        <input type="hidden" id="product" name="product_id[]" value="${ex_product.id}">
                  `
                  $(response.worker).each(function (key, extra) {
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
    
    
    
    
              } else {
    
                
                $('#transactionForm').modal('hide');
                $('#CommissionModal').modal('show');
                // window.location.reload();
    
                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });
    
              $.ajax({
                  type: "post",
                  url: "transaction-detail",
                  data: {
                      id: response.data.id
                  },
                  dataType: "json",
                  success: function (response) {
                    console.log(response);
                      // console.log($("#transaction_id").val());
                      $("#tgl_transaksi").html(`Tanggal Transaksi: &nbsp; ${response.tanggal_transaksi}`);
                $("#plat_nomor").html(`NOPOL: &nbsp; ${response.transaction.customer}`);
                $("#service").html(`Layanan: <ul id="buyservice"></ul>`);
                $(response.grouped_product).each(function (key, grouped) {
                  console.log(grouped);
                    $("#buyservice").append(`<li><b>${grouped.employee_products.service} &nbsp; &nbsp; (Rp ${grouped.employee_products.price.toLocaleString("id-ID")})</b></li>`);
                    // var service = buy_service.service;
                });
                $("#total_harga").html(`Total: &nbsp; Rp ${response.transaction.total_price.toLocaleString("id-ID")}`);
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
                              `<li>${product.employee_products.service}</li>`;
                          console.log(`<li>${product.employee_products.service}</li>`);
                          // commission = commission + ``;
                      });
                      append = append + services + '</ul></td><td>' + 'Rp ' + total_commission
                          .toLocaleString('id-ID') + '</td></tr>'
                      
                      $("#penggarap").append(append);
                      $("#total_komisi").html(
                    `Rp ${response.transaction.comission.toLocaleString('id-ID')}`);
                
                });
    
                      // if (response.commiss_check > 0) {
                      //   $(response.worker).each(function (key, workers) {
    
                      //     $("#penggarap").append(`<tr>
                      //                                 <td>${workers.name} ${workers.pivot.status == 'extra' ? `(${response.extra_product})` : ''}</td>
                      //                                 <td>Rp ${workers.pivot.commission.toLocaleString("id-ID")}</td>
                      //                             </tr>`);
                      //     $("#total_komisi").html(`Rp ${response.transaction.comission.toLocaleString('id-ID')}`);
                      // });
                      // } else {
                      //   $(response.worker).each(function (key, workers) {
                      //     $("#penggarap").append(`<tr>
                      //                                 <td>${workers.name}</td>
                      //                                 <td>Rp ${response.commission.toLocaleString('id-ID')}</td>
                      //                             </tr>`);
                      //     $("#total_komisi").html(`Rp ${response.transaction.comission.toLocaleString('id-ID')}`);
                      // });
                      // }
                    
    
                    }
                  });
                  
                }
            
            
          }

          
        } 
      });
      
    }
      
    });

  });
  
</script>