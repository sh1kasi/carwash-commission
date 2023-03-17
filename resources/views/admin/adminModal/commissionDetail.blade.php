<div class="modal fade" id="CommissionModal" tabindex="-1" role="dialog" data-bs-keyboard="false"
    data-bs-backdrop="static" aria-labelledby="CommissionModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CommissionModalLabel">Detail Transaksi</h5>
                <button type="button" class="btn-close" onclick="reload()"
                     aria-label="Close"></button>
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
                        <th>Layanan yang dikerjakan</th>
                        <th>Komisi</th>
                    </tr>
                </thead>
                <tbody id="penggarap">

                </tbody>
                <tfoot class="table-primary">
                    <tr>
                        <th colspan="2">Total Komisi</th>
                        <th id="total_komisi"></th>
                    </tr>
                </tfoot>
            </table>

            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button> --}}
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#commissionModal').on('hidden', function () {
            document.location.reload();
        })

        $('#commissionModal').on('hidden.bs.modal', function () {
            if (!$('#commissionModal').hasClass('no-reload')) {
                location.reload();
            }
        });

    });

    function reload(){
        window.location.href="{{ route('transaction.index') }}"
    }

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
                // console.log(response.product);

                // console.log($("#transaction_id").val());
                $("#tgl_transaksi").html(`Tanggal Transaksi: &nbsp; ${response.tanggal_transaksi}`);
                $("#plat_nomor").html(`NOPOL: &nbsp; ${response.transaction.customer}`);
                $("#service").html(`Layanan: <ul id="buyservice"></ul>`);
                $(response.grouped_product).each(function (key, grouped) {
                    // console.log(buy_service);
                    $("#buyservice").append(
                        `<li><b>${grouped.employee_products.service} &nbsp; &nbsp; (Rp ${grouped.employee_products.price.toLocaleString("id-ID")})</b></li>`
                        );
                    // var service = buy_service.service;
                });
                $("#total_harga").html(
                    `Total: &nbsp; Rp ${response.transaction.total_price.toLocaleString("id-ID")}`);
                $("#penggarap").html("");

                console.log(service);

                // $(response.worker).each(function (key, workers) {


                //   $.each(response.per_produk, function (key, product) { 
                //     console.log(response.per_produk);
                //   $("#penggarap").append(`<tr>
                //       <td>${workers.employees.name} (${product.products})</td>
                //       <td>Rp ${workers.commission.toLocaleString('id-ID')}</td>
                //       </tr>`);
                //       $("#total_komisi").html(`Rp ${response.transaction.comission.toLocaleString('id-ID')}`);

                //     });
                // });
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

                // if (response.commiss_check > 0) {
                //   $(response.worker).each(function (key, workers) {
                //     console.log(workers.employees.name);
                //     $("#penggarap").append(`<tr>
                //                                 <td>${workers.employees.name} (${workers.employee_products.service})</td>
                //                                 <td>Rp ${workers.commission.toLocaleString("id-ID")} ${workers.status == 'extra' ? `(Rp ${response.extra_commission.toLocaleString('id-ID')} / ${response.commiss_check} + Rp ${response.normal_price.toLocaleString('id-ID')})` 

                //                                   : `(Rp ${response.normal_price.toLocaleString("id-ID")} X ${response.persenan}% / ${response.total_worker})`} </td>
                //                             </tr>`);
                //     $("#total_komisi").html(`Rp ${response.transaction.comission.toLocaleString('id-ID')}`);
                // });
                // } else {
                //   $(response.worker).each(function (key, workers) {
                //     $("#penggarap").append(`<tr>
                //                                 <td>${workers.employees.name} (${workers.employee_products.service})</td>
                //                                 <td>Rp ${response.commission.toLocaleString('id-ID') } (Rp ${response.normal_price.toLocaleString("id-ID")} X 30% / ${response.total_worker})</td>
                //                             </tr>`);
                //     $("#total_komisi").html(`Rp ${response.transaction.comission.toLocaleString('id-ID')}`);
                // });
                // }


            }
        });

    }

</script>
