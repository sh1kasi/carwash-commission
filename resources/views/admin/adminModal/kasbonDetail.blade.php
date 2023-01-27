<div class="modal fade" id="detailKasbon" role="dialog" aria-labelledby="detailKasbonLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailKasbonLabel">Detail Kasbon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">

                <table id="tableCommission" class="display table table-bordered m-2 pe-3" style="width:98%">
                    <thead>
                        <tr class="table-primary">
                            <th>Tanggal Input Kasbon</th>
                            <th>Nominal Kasbon</th>
                        </tr>
                        {{-- <tr id="detail_kasbon">
                            
                        </tr> --}}
                    </thead>
                    <tbody id="detail_kasbon">  
    
                    </tbody>
                    <tfoot class="table-primary">
                        <tr>
                            <th colspan="1">Sisa Kasbon Bulan Ini: </th>
                            <th id="sisa_kasbon"></th>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <div class="modal-footer">
                {{-- <button type="submit" id="submit" class="btn btn-primary">Simpan Transaksi</button> --}}
                {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="tgl_input" id="tgl_input" value="">

<script>

    function detailKasbon(id, tgl_input) {

        console.log(tgl_input);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $.ajax({
            type: "post",
            url: "/kasbon/detail",
            data: {
                id: id,
                tgl_input: tgl_input
            },
            dataType: "json",
            success: function (response) {
                
                $("#detail_kasbon").html("");
                $(response.kasbon_employee).each(function (key, kasbon) {
                    var total_kasbon = kasbon.nominal;
                   $("#detail_kasbon").append(`
                        <tr>
                            <td style="text-align: center">${kasbon.tanggal}</td>
                            <td style="text-align: center">Rp ${kasbon.nominal.toLocaleString('id-ID')}</td>
                        </tr>
                   `);
                });
                $("#sisa_kasbon").html(`Rp ${response.sisa_nominal.toLocaleString('id-ID')}`);
                console.log(total_kasbon);

            }
        });

    }

</script>