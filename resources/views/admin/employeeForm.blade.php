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
                        <form action="/employee/form/post" method=post>
                            @csrf
                            <div class="mb-3">
                              <label for="exampleInputEmail1" class="form-label">Nama Pegawai</label>
                              <input type="text" id="name" value="" name="name" class="form-control" id="exampleInputEmail1" placeholder="Masukkan Nama Pegawai" aria-describedby="emailHelp">
                            </div>
                            <button type="submit" id="submit" class="btn btn-primary mt-3">Submit</button>
                          </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection