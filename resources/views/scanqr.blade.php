<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>scanqr</title>
</head>
<body>
    <div class="scanqr">
        {{-- <div class="scanmotor col-md-6">
            <img src="{{ asset('image/scanmotor.png') }}" style="width:200px" alt="motor">
        </div> --}}
        {{-- <div class="scanmobil col-md-6">
            <img src="{{ asset('image/scanmobil.png') }}" style="width:200px" alt="mobil">
        </div> --}}
        {{-- <div class="scanmobil">
            <h1>Link Mobil</h1>
            <p>{!! DNS2D::getBarcodeHTML(route('customer.mobil'), 'QRCODE'); !!}</p>
        </div> --}}
        <div class="scanmotor">
            <h1>Link Motor</h1>
            <p>{!! DNS2D::getBarcodeHTML(route('customer.motor'), 'QRCODE'); !!}</p>
        </div>
    </div>
</body>
</html>