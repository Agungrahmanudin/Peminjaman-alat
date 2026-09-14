<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Peminjaman Alat | Login</title>
    <link rel="stylesheet" href="{{ asset('tugas/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('tugas/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('tugas/assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('tugas/assets/css/style.css') }}">
</head>

<body>
    <div class="main-wrapper login-body">
        <div class="login-wrapper">
            <div class="container col-xl-6  col-lg-12 col-md-9">
                <div class="loginbox">
                    <div class="login-right">
                        <div class="login-right-wrap">
                            <h1>Login</h1>
                            <p class="account-subtitle">Access to our dashboard</p>

                            <!-- Display validation errors -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Email / Username</label>
                                    <input type="text" name="login" class="form-control" value="{{ old('login') }}"
                                        required>

                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Password</label>
                                    <div class="pass-group">
                                        <input type="password" name="password" class="form-control pass-input" required>
                                        <span class="fas fa-eye toggle-password"></span>
                                    </div>
                                </div>

                                <button class="btn btn-lg btn-block btn-primary w-100" type="submit">Login</button>
                                <div class="login-or">

                                    <span class="or-line"></span>

                                </div>

                                <div class="social-login mb-3">
                                    <a href="/auth-google-redirect"
                                        class="google btn btn-lg btn-block btn-danger w-100 d-flex align-items-center justify-content-center">
                                        <i class="fab fa-google me-2"></i> Login with Google
                                    </a>
                                </div>

                                <div class="text-center dont-have">

                                    <a href="{{ route('register') }}">Register</a> |
                                    <a href="{{ url('/') }}">Beranda</a>


                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('tugas/assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('tugas/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('tugas/assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('tugas/assets/js/script.js') }}"></script>

    @include('partials.flash')
</body>

</html>