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
                        <form action="{{ route('bundle.post') }}" method=post>
                            @csrf
                            <div class="mb-3">
                              <label for="exampleInputEmail1" class="form-label">Nama Bundle</label>
                              <input type="text" id="name" value="" name="name" class="form-control" id="exampleInputEmail1" placeholder="Masukkan Nama Bundle" aria-describedby="emailHelp">
                            </div>
                           
                            <div class="row pt-1" id="bundling-checkbox">
                                <label class="form-check-label mt-2" style="font-size: 15px" for="defaultCheck1">Layanan</label>
                                    @foreach ($product as $service)   
                                      <div class="form-group mt-1 col-md-4 mb-3">
                                        <input class="form-check-input cbservice " type="checkbox" name="servicesCheckbox[]" value="{{ $service->id }}" id="servicesCheckbox">
                                        <label class="form-check-label ps-2" style="font-size: 15px" for="defaultCheck1">{{ $service->service }} (@currency($service->price))</label>
                                      </div>
                                    @endforeach
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