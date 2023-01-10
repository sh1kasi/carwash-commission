<!-- Modal -->
<div class="modal fade" id="transactionForm" tabindex="-1" role="dialog" aria-labelledby="transactionFormLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 700px" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transactionFormLabel">TAMBAH TRANSAKSI</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"></span>
        </button>
      </div>
      <div class="modal-body">
        
        <div class="form-group pb-1">
          <label for="exampleInputEmail1"><b>NO PLAT KENDARAAN</b></label>
          <input type="text" name="nopol" class="form-control mt-1 mb-2" id="nopol" aria-describedby="emailHelp" placeholder="Masukkan nomor plat kendaraan">
        </div>
        
        <div class="row pt-1">
          <label class="form-check-label mt-2" style="font-size: 15px" for="defaultCheck1"><b>LAYANAN</b></label>
          @foreach ($product as $service)   
            <div class="form-group mt-1 col-md-6">
              <input class="form-check-input cbservice" type="checkbox" name="servicesCheckbox" onclick="servicesCheckbox({{ $service->id }})" value="{{ $service->id }}" id="servicesCheckbox">
              <label class="form-check-label ps-2" style="font-size: 15px" for="defaultCheck1">{{ $service->service }} (@currency($service->price))</label>
            </div>
          @endforeach
        </div>

        <div class="row pt-2 pb-3" style="border-bottom: 1px solid #c5bebefa"> 
          <label class="form-check-label mt-3" style="font-size: 15px" for="defaultCheck1"><b>PENGGARAP</b></label>
          @foreach ($employees as $employee)   
            <div class="form-group mt-1 col-md-6">
              <input class="form-check-input cbemployee" type="checkbox" value="{{ $employee->id }}" id="employee">
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="submit" class="btn btn-primary">Simpan Transaksi</button>
      </div>
    </div>
  </div>
</div>

<input type="hidden" value="" id="total_price">
<input type="hidden" value="" id="transaction_id">



<script>

  function servicesCheckbox(id) {
    var serviceArray = [];
    $(".cbservice:checkbox:checked").each(function () {
      serviceArray.push($(this).val());
    });
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
        },
        dataType: "json",
        success: function (response) {
          if (serviceArray.length > 0) {
            $("#amount").html(`Rp ${response.total_price.toLocaleString("id-ID")}`);
            $("#total_price").val(response.total_price);
          } else {
            $("#amount").html(`Rp 0`);
          }
        }
      });
  } 

  $(document).ready(function () {
    
    $("#submit").click(function (e) { 
      e.preventDefault();

      var nopol = $("#nopol").val();
      var service = $("#servicesCheckbox").val();
      var employee = $("#employee").val();
      var total_price = $("#total_price").val();

      var serviceArray = [];
      var employeeArray = [];

    $(".cbservice:checkbox:checked").each(function () {
      serviceArray.push($(this).val());
    });

    $(".cbemployee:checkbox:checked").each(function () {
      employeeArray.push($(this).val());
    });

  

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
          total_price: total_price
        },
        dataType: "json",
        success: function (response) {

          if (response.tambahan == true) {
            // window.location.reload();
            $("#transaction_id").val(response.data.id);
            $(response.worker).each(function (key, extra) {
              $("#extraWorks").append(`
              <input type="checkbox" name="inputExtra" value="${extra.id}" id="inputExtra">
              <label for="inputExtra" id="extraName">${extra.name}</label>  
              `);
            });
            $('#extraWorksModal').modal('show');
            $('#transactionForm').modal('hide');
          } else {
            window.location.reload();
          }
          
        } 
      });

      
    });

  });
  
</script>