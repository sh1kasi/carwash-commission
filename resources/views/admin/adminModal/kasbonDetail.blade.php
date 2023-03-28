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

                <div class="d-flex justify-content-evenly input-daterange" id="angin">
                    <div class="from_date d-flex">                              
                        <p style="width: 155px; font-size: 12px">Dari tanggal: </p>
                        <input type="text" class="form-control mb-3" name="from" id="from_date">
                    </div>
                    <div class="to_date d-flex ms-2">
                        <p style=" font-size: 12px">Hingga tanggal: </p>
                        <input type="text" class="form-control mb-3 ms-1" name="to" value="" id="to_date">
                        <button class="btn btn-primary mb-3 ms-1" type="button" id="search_date"><i class="fa fa-search" aria-hidden="true"></i></i></button>
                        {{-- <button class="btn btn-success mb-3 ms-2" type="button" id="print_pdf"><i class="fa fa-download" aria-hidden="true"></i></button> --}}
                        <button class="btn btn-warning mb-3 ms-1" type="button" id="refresh"><i class="fa fa-refresh" aria-hidden="true"></i></i></button>
                    </div>
                </div>

                <table id="kasbonDetailTable" class="display table table-bordered m-2 pe-3" style="width:98%">
                    <thead>
                        <tr class="pt-3 table-primary">
                            <th>Tanggal Input Kasbon</th>
                            <th>Nominal Kasbon</th>
                            <th>Sisa Kasbon Bulan Ini</th>
                        </tr>
                        {{-- <tr id="detail_kasbon">
                            
                        </tr> --}}
                    </thead>
                    <tbody id="detail_kasbon">  
                        
                    </tbody>
                    <tfoot class="table-primary">
                        <tr class="d-none" id="footerTable">
                            <th colspan="1">Kasbon yang tersisa: </th>
                            <td id="sisa_kasbon"></td>
                            <td id="sisa_nominal"></td>
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

        $(document).ready(function () {
            $("#angin").datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayBtn: "linked",
                // startView: 'months',
                // minViewMode: 'months'
                
            });
        });

        
    function detailKasbon(id, tgl_input) {
        console.log(id, tgl_input);
            $(document).ready(function () {
            
            console.log(tgl_input);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            load_data();

            $("#search_date").click(function (e) { 
                e.preventDefault();
                var from_date = $("#from_date").val();
                var to_date = $("#to_date").val();
                if (from_date != '' && to_date != '') {
                    $("#kasbonDetailTable").DataTable().destroy();
                    load_data(from_date, to_date);
                } else {
                     toastr.error("Harap isi kedua tanggal tanggal tersebut!");
                }
            });

            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();


            $("#refresh").click(function (e) { 
                e.preventDefault();
                $("#from_date").val('');
                $("#to_date").val('');
                $("#kasbonDetailTable").DataTable().destroy();
                load_data();
            });

            function load_data(from_date = '', to_date = '') {

                if (from_date == '' && to_date == '' ) {
                    $("#footerTable").addClass('d-none');
                } else {
                    $("#footerTable").removeClass('d-none');
                }

                $("#kasbonDetailTable").DataTable({
                    processing: true,
                    serverSide: true, 
                    filter: true,
                    searching: false,
                    paging: true,
                    pageLength: 10,
                    bInfo: false,
                    destroy: true,
                    language: {
                        emptyTable: 'Kasbon kosong'
                    },
                    ajax: {
                        type: 'GET',
                        url: 'kasbon/detail',
                        data: {
                            id: id,
                            from_date: from_date,
                            to_date: to_date,
                            // tgl_input: tgl_input,
                        }
                    },
                    "drawCallback": function(settings) {
                        $("#sisa_kasbon").html(`Rp ${settings.json.sisa_nominal.toLocaleString('id-ID')}`);
                    },
                    columns: [
                        {data: 'tgl_input', name: 'Tanggal Input Kasbon'},
                        {data: 'nominal_kasbon', name: 'Nominal Kasbon'},
                        {data: 'sisa_kasbon', name: 'Sisa Kasbon Bukan Ini'},
                    ],
                });
            }
            

            //     $.ajax({
            //     type: "post",
            //     url: "/kasbon/detail",
            //     data: {
            //         id: id,
            //         tgl_input: tgl_input
            //     },
            //     dataType: "json",
            //     success: function (response) {

            //         $("#sisa_kasbon").html("");
            //         $("#sisa_kasbon").html(`Rp ${response.sisa_nominal.toLocaleString('id-ID')}`);
            //         // console.log(total_kasbon);

            //     }
            // });
        });

    }

</script>