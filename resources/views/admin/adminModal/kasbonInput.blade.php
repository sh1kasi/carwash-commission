<form action="{{ route('kasbon.input') }}" method="post">
    @csrf
    <div class="modal fade" id="inputKasbon" role="dialog" aria-labelledby="inputKasbonLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 700px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputKasbonLabel">INPUT KASBON </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body row">

                    <ul id="err_list"></ul>

                    <div class="form-group pb-1 input-daterange col-md-6">
                        <label for="exampleInputEmail1"><b>Tanggal Input</b></label> <br>
                        <input type="text" class="form-control mt-2 mb-2" id="tgl_input"
                            style="text-transform: uppercase;" placehorer="Masukkan tanggal input kasbon"
                            aria-describedby="emailHelp" name="tgl_input"></input>
                        {{-- < name="nopol" class="form-control mt-1 mb-2" id="nopol" aria-describedby="emailHelp" placeholder="Masukkan nomor plat kendaraan"> --}}
                    </div>
                    <div class="form-group pb-1 col-md-6">
                        <label for="exampleInputEmail1"><b>Masukkan Nominal Kasbon</b></label> <br>
                        <input type="text" style="width: 100%;" name="nominal" class="form-control mt-2 mb-0" id="tgl_input"
                            style="text-transform: uppercase;" placehorer="Masukkan nominal kasbon"
                            aria-describedby="emailHelp"></input>
                            <small class="text-danger">*maksimal 100.000 rupiah</small>
                        {{-- < name="nopol" class="form-control mt-1 mb-2" id="nopol" aria-describedby="emailHelp" placeholder="Masukkan nomor plat kendaraan"> --}}
                    </div>

                    <input type="hidden" name="employee_id" id="employee_id" value="">
                    {{-- <input type="text" name="" id="kasbon_rest" > --}}
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submit" class="btn btn-primary">Simpan Inputan</button>
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('.input-daterange').datepicker({
            todayBtn: 'linked',
            format: 'yyyy-mm-dd',
            autoclose: true,
        });
    });
</script>
