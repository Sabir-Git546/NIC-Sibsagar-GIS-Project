@extends('layouts.master')

@section('content')
<style>
.password-wrapper {
    position: relative;
}

.password-wrapper .form-control {
    padding-right: 45px;
}

.toggle-password {
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
    z-index: 10;
}

.toggle-password:hover {
    color: #000;
}
</style>
<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow">

                <div class="card-header">

                    <h4 class="mb-0">
                        Forgot Password
                    </h4>

                </div>

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
                                   value="{{ session('userid') }}"
                                   required>

                        </div>

                        <button type="submit"
                                class="btn btn-primary">

                            Generate OTP

                        </button>

                    </form>


                    {{-- STEP 2 : VERIFY OTP --}}
                    @if(session('userid'))

                        <hr>

                        <h5>

                            Verify OTP

                        </h5>

                        <form method="POST"
                              action="{{ route('password.verifyOtp') }}">

                            @csrf

                            <input type="hidden"
                                   name="userid"
                                   value="{{ session('userid') }}">

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

                            <button type="submit"
                                    class="btn btn-success">

                                Verify OTP

                            </button>

                        </form>

                    @endif


                    {{-- STEP 3 : RESET PASSWORD --}}
                    @if(session('otp_verified'))

                        <hr>

                        <h5>

                            Reset Password

                        </h5>

                        <form method="POST"
                              action="{{ route('password.reset') }}">

                            @csrf

                            <input type="hidden"
                                   name="userid"
                                   value="{{ session('userid') }}">

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