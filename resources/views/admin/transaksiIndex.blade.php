@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    td {
        align-content: center;
    }
</style>


{{-- @dd($bundleArray); --}}
<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Transaksi Terbaru
                            {{-- <span class="position-absolute top-700 end-100 translate-middle badge rounded-pill bg-danger">
                                99+
                                <span class="visually-hidden">unread messages</span> --}}
                        </h5>
                        
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="left: 450px; position:absolute; top: 30px"></button>
                                    <div class="form-group pb-1">
                                        <label for="exampleInputEmail1"><b>NO PLAT KENDARAAN</b></label> <br>
                                      </div>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group pb-1">
                                        <div class="mb-3">
                                            {{-- <label for="exampleInputEmail1" class="form-label">Email address</label> --}}
                                            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                                            {{-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> --}}
                                          </div>
                                        {{-- <select style="width: 440px;" class="form-control mt-2 mb-2" id="nopol" style="text-transform: uppercase;" aria-describedby="emailHelp" name="nopol"></select> --}}
                                        {{-- < name="nopol" class="form-control mt-1 mb-2" id="nopol" aria-describedby="emailHelp" placeholder="Masukkan nomor plat kendaraan"> --}}
                                      </div>
                                </div>
                                <div class="modal-footer">
                                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                                <button type="button" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </div>
                            </div>
                        </div>

                        <table id="transaksiTable" class="display table table-striped table-hover"  style="width:100%; margin-top: 40px; text-align:center">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Nopol</th>
                                    <th class="text-center">Jenis Kendaraan</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Tanggal</th>
                                </tr>
                            </thead>
                            {{-- <tbody id="indexTable">
                                <tr>
                                    <td>1</td>
                                    <td>mobil/motor</td>
                                    <td>L 1234 KL</td>
                                    <td>
                                        <button type="button" class="btn btn-lg btn-success" style="font-size: 13px" disabled>sedang dikerjakan</button><br>
                                        <button type="button" class="btn btn-danger btn-lg" style="font-size: 13px" >belum ditransaksi</button>
                                    </td>
                                </tr>
                            </tbody> --}}
                        </table>
                </div>      
            </div>
        </div>
    </div>
</div>

<script> 


    $(document).ready(function () {
        // $('#nopol').select2(selectOption); 
        
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        // load_data();

        
        // function load_data(from_date = '', to_date = '') {
            
            $('#transaksiTable').DataTable({
                processing: true,
                serverSide: true,
                filter: true,
                searching: false,
                
                ajax: {
                type: 'GET',
                url: '/transaksi/json',
                // data: {
                // from_date: from_date,
                // to_date: to_date,
                // }
              },
              columns: [
                  {data: 'DT_RowIndex', name: '#'},
                  {data: 'nopol', name: 'nopol'},
                  {data: 'jenis_kendaraan', name: 'jenis_kendaraan'},
                  {data: 'keterangan', name: 'keterangan'},
                  {data: 'date', name: 'date'},
                ]
            });
            
        // }
        
    });

    function transactionInput(id) {
        var tanggal = $(`#pending${id}`).attr('data-date');
        var nopol = $(`#pending${id}`).attr('data-nopol');
        var transaksi_id = $(`#pending${id}`).attr('data-transaksiID');

        console.log(tanggal);
        console.log(nopol);

        window.location.href = (`/transaction?tanggal=${tanggal}&nopol=${nopol}&transaksi_id=${transaksi_id}`);


    }



</script>

@endsection