@php
    $hideNavbar = true;
@endphp

@extends('layouts.master')

@section('styles')

<link rel="stylesheet"
      href="{{ asset('css/otpGeneration.css') }}">

@endsection

@section('content')

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow">

                <div class="card-header">

                    <h4 class="mb-0">
                        Forgot Password
                    </h4>

                </div>

                <!--    <div class="alert alert-info">
                            auth_step = {{ session('auth_step') }}
                            <br>
                            auth_user = {{ session('auth_user') }}
                        </div>  -->

                <div class="card-body">

                    {{-- SUCCESS MESSAGE --}}
                    @if(session('success'))

                        <div class="alert alert-success alert-dismissible fade show">

                            {{ session('success') }}

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert">
                            </button>

                        </div>

                    @endif


                    {{-- ERROR MESSAGE --}}
                    @if(session('error'))

                        <div class="alert alert-danger alert-dismissible fade show">

                            {{ session('error') }}

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert">
                            </button>

                        </div>

                    @endif


                    {{-- VALIDATION ERRORS --}}
                    @if($errors->any())

                        <div class="alert alert-warning alert-dismissible fade show">

                            <ul class="mb-0">

                                @foreach($errors->all() as $error)

                                    <li>{{ $error }}</li>

                                @endforeach

                            </ul>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert">
                            </button>

                        </div>

                    @endif


                    {{-- STEP 1 : GENERATE OTP --}}
                   @if(!session('auth_step'))

                    <form method="POST"
                        action="{{ route('password.sendOtp') }}">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label">
                                User ID
                            </label>

                            <input type="text"
                                name="userid"
                                class="form-control"
                                required>

                        </div>

                        <button type="submit"
                                class="btn btn-primary">

                            Generate OTP

                        </button>

                    </form>

                    @endif

                    {{-- STEP 2 : VERIFY OTP --}}
                    @if(session('auth_step') === 'reset_otp')

                        <hr>

                        <h5>

                            Verify OTP

                        </h5>

                        <form method="POST"
                              action="{{ route('password.verifyOtp') }}">

                            @csrf

                            <input type="hidden"
                                   name="userid"
                                   value="{{ session('auth_user') }}">

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

                                    <a href="{{ route('password.cancel') }}"
                                    class="btn btn-secondary w-100">

                                        Cancel

                                    </a>

                                </div>

                                <div class="col-6">

                                    <button type="submit"
                                            class="btn btn-success w-100">

                                        Verify OTP

                                    </button>

                                </div>

                            </div>

                        </form>

                    @endif


                    {{-- STEP 3 : RESET PASSWORD --}}
                    @if(session('auth_step') === 'reset_password')

                        <hr>

                        <h5>

                            Reset Password

                        </h5>

                        <form method="POST"
                              action="{{ route('password.reset') }}">

                            @csrf

                            <input type="hidden"
                                   name="userid"
                                   value="{{ session('auth_user') }}">

                            <div class="mb-3">

                                <label class="form-label">

                                    New Password

                                </label>

                                <div class="password-wrapper">

                                    <input type="password"
                                        name="password"
                                        id="newPassword"
                                        class="form-control"
                                        required>

                                    <span class="toggle-password"
                                        id="toggleNewPassword">

                                        <i class="bi bi-eye"></i>

                                    </span>

                                </div>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">

                                    Confirm Password

                                </label>

                                <div class="password-wrapper">

                                    <input type="password"
                                        name="password_confirmation"
                                        id="confirmPassword"
                                        class="form-control"
                                        required>

                                    <span class="toggle-password"
                                        id="toggleConfirmPassword">

                                        <i class="bi bi-eye"></i>

                                    </span>

                                </div>

                            </div>

                            <button type="submit"
                                    class="btn btn-primary">

                                Reset Password

                            </button>

                        </form>

                    @endif


                    <hr>

                    <div class="text-center">

                        <a href="{{ route('login') }}">

                            Back to Login

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

    setTimeout(() => {

        let alerts =
            document.querySelectorAll('.alert');

        alerts.forEach(alert => {

            let bsAlert =
                bootstrap.Alert.getOrCreateInstance(alert);

            bsAlert.close();

        });

    }, 5000);

</script>
<script>

    function setupPasswordToggle(toggleId, inputId)
    {
        const toggle =
            document.getElementById(toggleId);

        const input =
            document.getElementById(inputId);

        if (!toggle || !input) {
            return;
        }

        toggle.addEventListener('click', function () {

            const type =
                input.getAttribute('type') === 'password'
                ? 'text'
                : 'password';

            input.setAttribute('type', type);

            this.innerHTML =
                type === 'password'
                ? '<i class="bi bi-eye"></i>'
                : '<i class="bi bi-eye-slash"></i>';

        });
    }

    setupPasswordToggle(
        'toggleNewPassword',
        'newPassword'
    );

    setupPasswordToggle(
        'toggleConfirmPassword',
        'confirmPassword'
    );

</script>


@endsection