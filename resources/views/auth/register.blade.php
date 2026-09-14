<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Peminjaman Alat | Register</title>
    <link rel="stylesheet" href="{{ asset('tugas/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('tugas/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('tugas/assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('tugas/assets/css/style.css') }}">
</head>

<body>

    <div class="main-wrapper login-body">
        <div class="login-wrapper">
            <div class="container">
                <div class="loginbox">
                    <div class="login-right">
                        <div class="login-right-wrap">
                            <h1>Register</h1>
                            <p class="account-subtitle">Access to our dashboard</p>

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="form-group">
                                    <label class="form-control-label">Name</label>
                                    <input class="form-control @error('name') is-invalid @enderror"
                                           type="text" name="name"
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Username</label>
                                    <input class="form-control @error('username') is-invalid @enderror"
                                           type="text" name="username"
                                           value="{{ old('username') }}" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Email Address</label>
                                    <input class="form-control @error('email') is-invalid @enderror"
                                           type="email" name="email"
                                           value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">No HP</label>
                                    <input class="form-control @error('phone') is-invalid @enderror"
                                           type="text" name="phone"
                                           value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Password</label>
                                    <input class="form-control @error('password') is-invalid @enderror"
                                           type="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Confirm Password</label>
                                    <input class="form-control" type="password" name="password_confirmation" required>
                                </div>

                                <div class="form-group mb-0">
                                    <button class="btn btn-lg btn-block btn-primary w-100" type="submit">Register</button>
                                </div>
                            </form>

                            <div class="login-or">
                                <span class="or-line"></span>
                            </div>

                            <div class="social-login mb-3">
                                <a href="{{ route('auth-google-redirect') }}"
                                    class="google btn btn-block btn-danger btn-fab w-100 d-flex align-items-center justify-content-center">
                                    <i class="fab fa-google me-2"></i> Register with Google
                                </a>
                            </div>

                            <div class="text-center dont-have">
                                <a href="{{ route('login') }}">Login</a> |
                                <a href="{{ url('/') }}">Beranda</a>
                            </div>
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