<div class="modal fade" id="CommissionModal" tabindex="-1" role="dialog" aria-labelledby="CommissionModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="CommissionModalLabel">Detail Transaksi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <b id="tgl_transaksi"></b> <br>
            <b id="plat_nomor"></b><br>
            <b id="service"></b>
            <b id="total_harga"></b>
        </div>

        <table id="tableCommission" class="display table table-bordered m-2 pe-3" style="width:98%">
            <thead class="table-primary">
                <tr>
                    <th>Penggarap</th>
                    <th>Komisi</th>
                </tr>
            </thead>
            <tbody id="penggarap">

            </tbody>
            <tfoot class="table-primary">
                <tr>
                    <th>Total Komisi</th>
                    <th id="total_komisi"></th>
                </tr>
            </tfoot>
        </table>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

<script>
   
   function commissionDetail(id) {

    $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

    $.ajax({
        type: "post",
        url: "transaction-detail",
        data: {
            id: id
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            $("#tgl_transaksi").html(`Tanggal Transaksi: &nbsp; ${response.tanggal_transaksi}`);
            $("#plat_nomor").html(`NOPOL: &nbsp; ${response.transaction.customer}`);
            $("#service").html(`Layanan: <ul id="buyservice"></ul>`);
            $(response.product).each(function (key, buy_service) {
                // console.log(buy_service);
                $("#buyservice").append(`<li><b>${buy_service.service} &nbsp; &nbsp; (Rp ${buy_service.price.toLocaleString("id-ID")})</b></li>`);
            });
            $("#total_harga").html(`Total: &nbsp; Rp ${response.transaction.total_price.toLocaleString("id-ID")}`);
            $("#penggarap").html("");
            $(response.worker).each(function (key, workers) {
                $("#penggarap").append(`<tr>
                                            <td>${workers.name}</td>
                                            <td>Rp ${response.commission.toLocaleString('id-ID')}</td>
                                        </tr>`);
                $("#total_komisi").html(`Rp ${response.transaction.comission.toLocaleString('id-ID')}`);
            });
        }
    });

   }

</script>