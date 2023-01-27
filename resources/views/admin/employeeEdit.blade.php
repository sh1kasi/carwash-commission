@extends('layouts.admin')

@section('content')
    
<div class="page-content">
    <div class="main-wrapper">
        <div class="row">
            <div class="col">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <ul>
                                <li>{{ $error }}</li>
                            </ul>
                        @endforeach
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tambah Layanan</h5>
                        <form action="/employee/update/{{ $employee->id }}" method=post>
                            @csrf
                            <div class="mb-3">
                              <label for="exampleInputEmail1" class="form-label">Nama Pegawai</label>
                              <input type="text" id="name" value="{{ $employee->name }}" name="name" class="form-control" id="exampleInputEmail1" placeholder="Masukkan Nama Pegawai" aria-describedby="emailHelp">
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role Pegawai</label>
                                <select class="form-control" name="role" id="role">
                                  <option>Pilih Role Pegawai</option>
                                  <option {{ $employee->role == 'Tetap' ? "selected" : '' }} value="Tetap">Tetap</option>
                                  <option {{ $employee->role == 'Training' ? "selected" : '' }} value="Training">Training</option>
                                  <option {{ $employee->role == 'Freelance' ? "selected" : '' }} value="Freelance">Freelance</option>
                                </select>
                            </div>
                            <div class="{{ $employee->kasbon != null ? 'mb-3' : 'mb-3 d-none' }}" id="input_kasbon">
                                <label for="kasbon" class="form-label">Nominal Maksimal Kasbon</label>
                                <input type="text" id="kasbon" value="{{ $employee->kasbon }}" name="kasbon" class="form-control" id="kasbon" placeholder="Masukkan maksimal kasbon" aria-describedby="emailHelp">
                            </div>
                            <button type="submit" id="submit" class="btn btn-primary mt-3">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" value="{{ $employee->kasbon }}" id="returnKasbon">

<script>

$("#role").change(function (e) { 
        e.preventDefault();
        if ($("#role").val() == 'Tetap') {
            $("#input_kasbon").removeClass('d-none');
            $("#kasbon").val($("#returnKasbon").val());
        } else {
            $("#input_kasbon").addClass('d-none');
            $("#kasbon").val('');
        }
    });

</script>

@endsection