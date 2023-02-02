<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Responsive Admin Dashboard Template">
        <meta name="keywords" content="admin,dashboard">
        <meta name="author" content="stacks">
        <!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        
        <!-- Title -->
        <title>Circl - Responsive Admin Dashboard Template</title>

        <!-- Styles -->
        <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700,800&display=swap" rel="stylesheet">
        <link href="{{ asset('template') }}/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('template') }}/plugins/font-awesome/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="{{ asset('template') }}/plugins/perfectscroll/perfect-scrollbar.css" rel="stylesheet">

      
        <!-- Theme Styles -->
        <link href="{{ asset('template') }}/css/main.min.css" rel="stylesheet">
        <link href="{{ asset('template') }}/css/custom.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="login-page">
        <div class='loader'>
            <div class='spinner-grow text-primary' role='status'>
              <span class='sr-only'>Loading...</span>
            </div>
          </div>
        <div class="container">
            <div class="row justify-content-md-center">
                <div class="col-md-12 col-lg-4">
                    <div class="card login-box-container">
                        <div class="card-body">
                            <div class="authent-logo">
                                <img src="{{ asset('template') }}/images/logo@2x.png" alt="">
                            </div>
                            <div class="authent-text">
                                <p>Selamat Datang di Mastrip37</p>
                                <p>Silakan Masuk ke akun anda.</p>
                            </div>

                            <form method="post" action="/login/store">
                                @csrf
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="text" name="name" class="form-control" id="floatingInput" placeholder="name@example.com">
                                        <label for="floatingInput">Username</label>
                                      </div>
                                      @error('name')
                                      <div class="text-danger mt-2">
                                        {{ $message }}
                                      </div>
                                      @enderror
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
                                        <label for="floatingPassword">Password</label>
                                      </div>
                                      @error('password')
                                      <div class="text-danger mt-2">
                                        {{ $message }}
                                      </div>
                                      @enderror
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-info m-b-xs">Masuk</button>
                                </div>
                              </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <!-- Javascripts -->
        <script src="{{ asset('template') }}/plugins/jquery/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
        <script src="https://unpkg.com/@popperjs/core@2"></script>
        <script src="{{ asset('template') }}/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="https://unpkg.com/feather-icons"></script>
        <script src="{{ asset('template') }}/plugins/perfectscroll/perfect-scrollbar.min.js"></script>
        <script src="{{ asset('template') }}/js/main.min.js"></script>
    </body>
    @if (session()->has('failed'))
    <script>
     toastr.error("{!! session('failed') !!}", 'Peringatan!');
    </script>
    @endif


</html>