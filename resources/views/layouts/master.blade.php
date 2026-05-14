<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Sibsagar District GIS Portal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Global CSS -->
    <link rel="stylesheet"
          href="{{ asset('css/app.css') }}">

    {{-- PAGE STYLES --}}
    @yield('styles')

</head>

<body>

    <!-- =========================
         HEADER
    ========================= -->
    <div class="header py-3">

        <div class="container-fluid">

            <div class="row align-items-center">

                <!-- LOGO -->
                <div class="col-auto">

                    <img src="{{ asset('images/ashokaPiller.jpg') }}"
                         class="img-fluid"
                         style="max-height:60px;"
                         alt="Govt Logo">

                </div>


                <!-- HEADING -->
                <div class="col">

                    <h4 class="mb-0 text-white">
                        Govt. of Assam
                    </h4>

                    <h5 class="mb-0 text-white">
                        Sibsagar District Administration
                    </h5>

                </div>


                <!-- AUTH USER -->
                @auth

                    <div class="col-auto text-end">

                        <small class="text-white d-block">
                            Welcome, {{ Auth::user()->userid }}
                        </small>

                        <!-- LOGOUT -->
                        <form method="POST"
                              action="{{ route('logout') }}">

                            @csrf

                            <button type="submit"
                                    class="btn btn-sm btn-light mt-1">

                                Logout

                            </button>

                        </form>

                    </div>

                @endauth

            </div>

        </div>

    </div>


    <!-- =========================
         TOP NAVBAR
    ========================= -->
    @if(!isset($hideNavbar))

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

            <div class="container">

                <!-- BRAND -->
                <a class="navbar-brand"
                   href="{{ route('dashboard') }}">

                    Sibsagar District GIS Portal

                </a>

                <!-- MOBILE TOGGLE -->
                <button class="navbar-toggler"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#mainNavbar">

                    <span class="navbar-toggler-icon"></span>

                </button>


                <!-- NAVBAR -->
                <div class="collapse navbar-collapse"
                     id="mainNavbar">

                    <ul class="navbar-nav ms-auto">

                        <!-- HOME -->
                        <li class="nav-item">

                            <a class="nav-link navbar-tab {{ request()->routeIs('home') ? 'active' : '' }}"
                               href="{{ url('/') }}">

                                Home

                            </a>

                        </li>


                        <!-- LOGIN -->
                        @guest

                            <li class="nav-item">

                                <a class="nav-link navbar-tab {{ request()->routeIs('login') ? 'active' : '' }}"
                                   href="{{ route('login') }}">

                                    User Login

                                </a>

                            </li>

                        @endguest


                        <!-- ABOUT -->
                        <li class="nav-item">

                            <a class="nav-link navbar-tab {{ request()->is('about') ? 'active' : '' }}"
                               href="{{ url('/about') }}">

                                About Us

                            </a>

                        </li>


                        <!-- CONTACT -->
                        <li class="nav-item">

                            <a class="nav-link navbar-tab {{ request()->is('contact') ? 'active' : '' }}"
                               href="{{ url('/contact') }}">

                                Contact Us

                            </a>

                        </li>

                    </ul>

                </div>

            </div>

        </nav>

    @endif


    <!-- =========================
         MAIN CONTENT
    ========================= -->
    <div class="content py-5">

        @yield('content')

    </div>


    <!-- =========================
         FOOTER
    ========================= -->
    <div class="footer py-3 text-center">

        © {{ date('Y') }} Sibsagar District Administration

    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- PAGE SCRIPTS --}}
    @yield('scripts')

</body>

</html>