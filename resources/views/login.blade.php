<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>HeloTek - Login</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('public/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('public/css/app.min.css?'.time())}}" rel="stylesheet">
    <script src="{{asset('public/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('public/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{asset('public/vendor/jquery-easing/jquery.easing.min.js')}}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{asset('public/js/app.min.js?'.time())}}"></script>

</head>

<body class="bg-gradient-primary">

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-3">&nbsp;</div>
                        <div class="col-lg-6">
                            <div class="p-5 loginContainer">
                                <div class="text-center">
                                    <img src="{{ asset("public/images/logo.png") }}" class="logoImage"
                                         alt="logo.png">
                                </div>
                                <form class="user" method="POST" action="{{ route('admin.login')}}">
                                    @if ( $errors->has('email') ||
                                    $errors->has('password') ||
                                    session('status')
                                    )
                                        <div class="alert alert-danger">
                                            @if ( $errors->has('email'))
                                                {{ $errors->first('email') }}
                                                <br/>
                                            @endif
                                            @if ( $errors->has('password'))
                                                    {{ $errors->first('password') }}
                                                <br/>
                                            @endif
                                            @if (session('status'))
                                                    {{ session('status') }}
                                            @endif
                                        </div>
                                @endif
                                   
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user"
                                               aria-describedby="emailHelp"
                                               name="email"
                                               value="{{ old("email") }}"
                                               placeholder="Enter Email Address...">
                                    </div>

                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user"
                                               name="password" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" name="remember" class="custom-control-input" id="customCheck" {{ old('remember') ? 'checked': '' }}>
                                            <label class="custom-control-label" for="customCheck">Remember Me</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Login
                                    </button>
                                    <hr>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-3">&nbsp;</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
</body>

</html>
