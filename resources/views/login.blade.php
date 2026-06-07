<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        RBAC Login | Spatial Information System
    </title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">


    <!-- NO CACHE SECURITY -->
    <meta http-equiv="Cache-Control"
          content="no-cache, no-store, must-revalidate">

    <meta http-equiv="Pragma"
          content="no-cache">

    <meta http-equiv="Expires"
          content="0">


    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">


    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

</head>

<body>

<div class="portal-title">

    <h2>
        Spatial Information System
    </h2>

    <h5>
        Sibsagar District Administration GIS Portal
    </h5>

</div>


<div class="login-wrapper">

    <div class="card login-card">

        <!-- HEADER -->
        <div class="card-header text-center login-header py-3">

            <h4 class="mb-0">

                User Login

            </h4>

        </div>


        <!-- BODY -->
        <div class="card-body p-4">


            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))

                <div class="alert alert-success alert-dismissible fade show"
                     role="alert">

                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>

                </div>

            @endif


            {{-- ERROR MESSAGE --}}
            @if(session('error'))

                <div class="alert alert-danger alert-dismissible fade show"
                     role="alert">

                    {{ session('error') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>

                </div>

            @endif


            {{-- VALIDATION ERRORS --}}
            @if($errors->any())

                <div class="alert alert-warning alert-dismissible fade show"
                     role="alert">

                    <strong>

                        Please fix the following:

                    </strong>

                    <ul class="mb-0 mt-2">

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


            <!-- LOGIN FORM -->
            <form method="POST"
                  action="{{ route('login.submit') }}"
                  id="loginForm">

                @csrf


                <!-- USER ID -->
                <div class="mb-3">

                    <label class="form-label">

                        User ID

                    </label>

                    <input type="text"
                           name="userid"
                           class="form-control @error('userid') is-invalid @enderror"
                           value="{{ old('userid') }}"
                           placeholder="Enter your userid"
                           autocomplete="username"
                           required>

                </div>


                <!-- PASSWORD -->
                <div class="mb-3">

                    <label class="form-label">

                        Password

                    </label>

                    <div class="password-wrapper">

                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               required>

                        <!-- TOGGLE PASSWORD -->
                        <span class="toggle-password"
                              id="togglePassword">

                            <i class="bi bi-eye"></i>

                        </span>

                    </div>

                </div>


                <!-- CAPTCHA -->
                <div class="mb-4 text-center">

                    <div class="g-recaptcha d-inline-block"
                         data-sitekey="{{ config('services.recaptcha.site_key') }}">
                    </div>

                </div>


                <!-- LOGIN BUTTON -->
                <div class="d-grid">

                    <button type="submit"
                            class="btn btn-custom"
                            id="loginBtn">

                        Login

                    </button>

                </div>

            </form>

            <div class="text-end mt-4 ">

                <a href="{{ route('password.forgot') }}"
                class="text-decoration-none fs-5">

                    Forgot Password?

                </a>

            </div>

            <!-- FOOTER 
            <div class="text-center mt-4 text-muted small">

                Authorized Access Only

            </div>  -->

        </div>

    </div>

</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- Google reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js"
        async
        defer>
</script>


<script>

    /*
    |==================================================
    | AUTO HIDE ALERTS
    |==================================================
    */
    setTimeout(() => {

        let alerts = document.querySelectorAll('.alert');

        alerts.forEach(alert => {

            let bsAlert =
                bootstrap.Alert.getOrCreateInstance(alert);

            bsAlert.close();

        });

    }, 5000);



    /*
    |==================================================
    | DISABLE MULTIPLE SUBMIT
    |==================================================
    */
    document.getElementById('loginForm')

        .addEventListener('submit', function () {

            const btn =
                document.getElementById('loginBtn');

            btn.disabled = true;

            btn.innerHTML = 'Logging in...';

        });



    /*
    |==================================================
    | PASSWORD TOGGLE
    |==================================================
    */
    const togglePassword =
        document.getElementById('togglePassword');

    const password =
        document.getElementById('password');


    togglePassword.addEventListener('click', function () {

        const type =
            password.getAttribute('type') === 'password'
            ? 'text'
            : 'password';

        password.setAttribute('type', type);

        this.innerHTML =

            type === 'password'

            ? '<i class="bi bi-eye"></i>'

            : '<i class="bi bi-eye-slash"></i>';

    });

</script>

</body>
</html>