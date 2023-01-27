  <form action="/transaction-extraworkers" id="extraWorkerValidation" method="post">
    @csrf
    <div class="modal fade" id="extraWorksModal" tabindex="-1" role="dialog" data-bs-keyboard="false" data-bs-backdrop="static" aria-labelledby="extraWorksModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="extraWorksModalLabel">Pilih Penggarap Extra</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pekerja-extra mt-1" id="pekerjaExtra">
              
              <div id="extraWorks mt-2">
                
              </div>
    
            </div>
            <div class="modal-footer">
                <button type="submit" id="submitBtn" class="btn btn-primary disabled">Save changes</button>
            </div>
        </div>
    </div>
    <input type="hidden" name="transaction_id" id="extra_transaction_id">
  </form>

  <input type="hidden" name="extraArray[]" id="extraArray">

<script>



function extraWorkers(id, extraId) {
  var extra_workers = [];
  var product_extra = $("#extraArray").val();
  console.log(product_extra);
  
  $(".cbextra:checkbox:checked").each(function () {
    extra_workers.push($(this).val());
  })

  console.log(extra_workers.length);

  if (extra_workers.length === 0) {
    $("#submitBtn").addClass('disabled');
  } else {
    $("#submitBtn").removeClass('disabled');
  }
  
  $(document).ready(function () { 
    $('.simpan').click(function (e) { 
      e.preventDefault();
      var employee_id = $('input=[name=product]')
      var transaction_id = $("#transaction_id").val();

      console.log(extra_workers);

      $.ajax({
        type: "post",
        url: "/transaction-extraworkers",
        data: {
          extra: extra_workers,
          product_extra: product_extra,
          id: transaction_id,
        },
        success: function (response) {

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
                $('#extraWorksModal').modal('hide');
                $('#CommissionModal').modal('show');

                  // console.log($("#transaction_id").val());
                  $("#tgl_transaksi").html(`Tanggal Transaksi: &nbsp; ${response.tanggal_transaksi}`);
            $("#plat_nomor").html(`NOPOL: &nbsp; ${response.transaction.customer}`);
            $("#service").html(`Layanan: <ul id="buyservice"></ul>`);
            $(response.grouped_product).each(function (key, grouped) {
              // console.log(buy_service);
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
                        `<li>${product.employee_products.service} (Rp ${product.commission.toLocaleString('id-ID')})</li>`;
                    // console.log(`<li>${product.employee_products.service}</li>`);
                    // commission = commission + ``;
                });
                append = append + services + '</ul></td><td>' + 'Rp ' + total_commission
                    .toLocaleString('id-ID') + '</td></tr>'
                
                $("#penggarap").append(append);
                $("#total_komisi").html(
                `Rp ${response.transaction.comission.toLocaleString('id-ID')}`);
            
            });
                

              }
          });



          // window.location.reload();
        }
      });

    });
  });
  
  
}

</script>