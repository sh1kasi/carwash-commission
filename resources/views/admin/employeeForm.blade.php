@extends('layouts.admin')

@section('content')
    
<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <ul id="err_list">
                                <li>{{ $error }}</li>
                            </ul>
                        @endforeach
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tambah Layanan</h5>
                        <form action="/employee/form/post" id="employee_form" method=post>
                            @csrf
                            <div class="mb-3">
                              <label for="exampleInputEmail1" class="form-label">Nama Pegawai</label>
                              <input type="text" id="name" value="" name="name" class="form-control" id="exampleInputEmail1" placeholder="Masukkan Nama Pegawai" aria-describedby="emailHelp">
                            </div>
                            <div class="mb-3">
                              <label for="role" class="form-label">Role Pegawai</label>
                              <select class="form-control" name="role" id="role">
                                <option>Pilih Role Pegawai</option>
                                <option value="Tetap">Tetap</option>
                                <option value="Training">Training</option>
                                <option value="Freelance">Freelance</option>
                              </select>
                            </div> 
                             <div class="mb-3 d-none" id="input_kasbon">
                                <label for="kasbon" class="form-label">Nominal Maksimal Kasbon</label>
                                <input type="text" id="kasbon" value="" name="kasbon" class="form-control" id="kasbon" placeholder="Masukkan maksimal kasbon" aria-describedby="emailHelp">
                            </div>
                            <button type="submit" id="submit" class="btn btn-primary mt-3">Submit</button>
                          </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    // $("#submit").click(function (e) { 
    //     e.preventDefault();
    //     $("#employee_form").submit(function (e) { 
    //         e.preventDefault();
    //         alert('a');
    //     });
    // });

    $("#role").change(function (e) { 
        e.preventDefault();
        if ($("#role").val() == 'Tetap') {
            $("#input_kasbon").removeClass('d-none');
        } else {
            $("#input_kasbon").addClass('d-none');
        }
    });

</script>

@endsection