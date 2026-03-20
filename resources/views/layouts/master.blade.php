<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sibsagar District GIS Portal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Global CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Page Specific Styles --}}
    @yield('styles')
</head>

<body>

<!--Header section-->
<div class="header py-3">
    <div class="container-fluid">
        <div class="row align-items-center">

            <!--govt logo-->
            <div class="col-auto">
                <img src="{{ asset('images/ashokaPiller.jpg') }}"
                     class="img-fluid"
                     style="max-height:60px;">
            </div>

            <!--heading-->
            <div class="col">
                <h4 class="mb-0 text-white">Govt. of Assam</h4>
                <h5 class="mb-0 text-white">Sibsagar District Administration</h5>
            </div>

            <!--header welcome user and logout using userif from session-->
          {{-- @if(session()->has('userid') && (!isset($hideHeaderAuth) || $hideHeaderAuth == false)) --}}
           {{-- @if(session()->has('userid') && request()->routeIs('dashboard')) --}}
           @if(session()->has('userid'))
            <div class="col-auto text-end">

                <!--welcome msg-->
                <small class="text-white d-block">
                    Welcome, {{ session('userid') }}
                </small>

                <!--logout button-->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light mt-1">
                        Logout
                    </button>
                </form>

            </div>
            @endif
        </div>
    </div>
</div>

<!--top navbar only for public page-->
@if (!isset($hideNavbar))
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <div class="navbar-brand">Sibsagar District GIS Portal</div>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link navbar-tab {{ request()->is('/') ? 'active' : '' }}" href="/">
                        Home
                    </a>
                </li>

                @if(!session()->has('userid'))
                <li class="nav-item">
                    <a class="nav-link navbar-tab {{ request()->routeIs('login') ? 'active' : '' }}"
                    href="{{ route('login') }}">
                        User Login
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link navbar-tab {{ request()->is('about') ? 'active' : '' }}"
                    href="/about">
                        About Us
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link navbar-tab {{ request()->is('contact') ? 'active' : '' }}"
                    href="/contact">
                        Contact Us
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
@endif

<!--MAIN CONTENT-->
<div class="content py-5">
    @yield('content')
</div>

<!--footer-->
<div class="footer py-3 text-center">
    © {{ date('Y') }} Sibsagar District Administration
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Page Specific Scripts --}}
@yield('scripts')

</body>
</html>