@extends('layouts.master')

@php
$hideNavbar = true;
@endphp

@section('title', 'RBAC Login | Spatial Information System')

@section('styles') <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

<div class="page-background">


<div class="portal-title">

    <h2>
        Spatial Information System
    </h2>

    <h5>
        Sibsagar District Administration
    </h5>

</div>


<div class="announcement-bar">

    <div class="announcement-text">

        Welcome to the Spatial Information System Portal •
        
        Designed & Developed by NIC Sibsagar •

    </div>

</div>


<div class="login-wrapper">

    <div class="card login-card">

        <div class="card-header text-center login-header py-3">

            <h4 class="mb-0">
                User Login
            </h4>

        </div>

        <div class="card-body p-4">

            {{-- LOGIN FORM --}}
            @if(session('login_step') !== 'otp')

                <form method="POST"
                      action="{{ route('login.submit') }}"
                      id="loginForm">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            User ID
                        </label>

                        <input type="text"
                               name="userid"
                               class="form-control"
                               value="{{ old('userid') }}"
                               placeholder="Enter your User ID"
                               autocomplete="username"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Password
                        </label>

                        <div class="password-wrapper">

                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control"
                                   placeholder="Enter your password"
                                   autocomplete="current-password"
                                   required>

                            <span class="toggle-password"
                                  id="togglePassword">

                                <i class="bi bi-eye"></i>

                            </span>

                        </div>

                    </div>

                    <div class="mb-4 text-center">

                        <div class="g-recaptcha d-inline-block"
                             data-sitekey="{{ config('services.recaptcha.site_key') }}">
                        </div>

                    </div>

                    <div class="d-grid">

                        <button type="submit"
                                class="btn btn-custom"
                                id="generateOtpBtn">

                            Generate OTP

                        </button>

                    </div>

                </form>

            @endif


            {{-- OTP FORM --}}
            @if(session('login_step') === 'otp')

                <h5 class="mb-3">
                    Verify OTP
                </h5>

                <form method="POST"
                      action="{{ route('login.verifyOtp') }}">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            OTP
                        </label>

                        <input type="text"
                               name="otp"
                               class="form-control"
                               maxlength="6"
                               required>

                    </div>

                    <div class="row">

                        <div class="col-6">

                            <a href="{{ route('login.cancel') }}"
                               class="btn btn-secondary w-100">

                                Cancel

                            </a>

                        </div>

                        <div class="col-6">

                            <button type="submit"
                                    class="btn btn-custom w-100"
                                    id="verifyOtpBtn">

                                Verify OTP

                            </button>

                        </div>

                    </div>

                </form>

            @endif

            <div class="text-end mt-4">

                <a href="{{ route('password.forgot') }}"
                   class="text-decoration-none">

                    Forgot Password?

                </a>

            </div>

        </div>

    </div>

</div>
```

</div>

@endsection

@section('scripts')

<script src="https://www.google.com/recaptcha/api.js"
        async
        defer>
</script>

<script src="{{ asset('js/login.js') }}"></script>

@endsection
