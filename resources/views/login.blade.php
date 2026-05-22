<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RBAC Login | Spatial Information System</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Page CSS -->
    <link rel="stylesheet"
          href="{{ asset('css/login.css') }}">
</head>

<body>

<div class="portal-title">

    <h2>Government of Assam</h2>

    <h5>Sibsagar District Administration Portal</h5>

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
                  action="{{ route('login.submit') }}">

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

                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Enter your password"
                           autocomplete="current-password"
                           required>

                </div>


                <!-- CAPTCHA -->
                <div class="mb-4 text-center">

                    <div class="g-recaptcha d-inline-block"
                         data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}">
                    </div>

                </div>


                <!-- LOGIN BUTTON -->
                <div class="d-grid">

                    <button type="submit"
                            class="btn btn-custom">

                        Login

                    </button>

                </div>

            </form>

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

</body>
</html>