<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Sibsagar District GIS Portal</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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

                    <img src="{{ asset('images/logo1.png') }}"
                         class="img-fluid"
                         style="max-height:60px;"
                         alt="Govt Logo">

                </div>


                <!-- HEADING -->
                <div class="col">

                    <h4 class="mb-0 header-title fw-bold">
                        Sibsagar District Administration
                    </h4>

                    <h5 class="mb-0 header-title fw-bold">   
                        Govt. of Assam
                    </h5>

                </div>


                <!-- NIC Logo -->
                <div class="col text-end">
                    

                    <img src="{{ asset('images/nic-logo.png') }}"
                         class="img-fluid"
                         style="max-height:60px;"
                         alt="Govt Logo">
                </div>


                <!-- AUTH USER -->
                @auth

                    <div class="col-auto text-end">

                        <small class="text-black d-block">
                            Welcome, {{ Auth::user()->userid }}
                        </small>

                        <!-- LOGOUT -->
                        <form method="POST"
                              action="{{ route('logout') }}">

                            @csrf

                                <!-- Icon-Only Button for Navbars -->
                                <button type="Submit" class="btn btn-danger" aria-label="Log out">
                                <i class="bi bi-box-arrow-right fs-4"></i>
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

        <div class="container">

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

        </div>


        @yield('content')

    </div>


    <!-- =========================
         FOOTER
    ========================= -->

<footer class="footer bg-dark text-light mt-auto">

    <div class="container py-4">

        <div class="row">

            <!-- ABOUT -->
            <div class="col-md-3 mb-4">

                <h5 class="fw-bold border-bottom pb-2">
                    About Us
                </h5>

                <p class="small">
                    The Spatial Information System (SIS) is a web-based GIS
                    platform developed for Sibsagar District Administration
                    to facilitate spatial data management, analysis and
                    decision-making.
                </p>

            </div>


            <!-- QUICK LINKS -->
            <div class="col-md-3 mb-4">

                <h5 class="fw-bold border-bottom pb-2">
                    Quick Links
                </h5>

                <ul class="list-unstyled">

                    <li>
                        <a href="{{ url('/') }}"
                        class="footer-link">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/about') }}"
                        class="footer-link">
                            About Us
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/contact') }}"
                        class="footer-link">
                            Contact Us
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('login') }}"
                        class="footer-link">
                            User Login
                        </a>
                    </li>

                </ul>

            </div>


            <!-- HELP -->
            <div class="col-md-3 mb-4">

                <h5 class="fw-bold border-bottom pb-2">
                    Help & Support
                </h5>

                <ul class="list-unstyled">

                    <li>User Manual</li>

                    <li>Frequently Asked Questions</li>

                    <li>System Requirements</li>

                    <li>Technical Support</li>

                </ul>

            </div>


            <!-- CONTACT -->
            <div class="col-md-3 mb-4">

                <h5 class="fw-bold border-bottom pb-2">
                    Reach Out To Us
                </h5>

                <p class="small mb-1">
                    Deputy Commissioner's Office
                </p>

                <p class="small mb-1">
                    Sivasagar, Assam
                </p>

                <p class="small mb-1">
                    Email: support@sivasagar.gov.in
                </p>

                <p class="small mb-0">
                    Phone: +91-XXXXXXXXXX
                </p>

            </div>

        </div>

    </div>

    <!-- COPYRIGHT BAR -->
    <div class="bg-secondary text-center py-2">

        <small>
            © {{ date('Y') }} National Informatics Centre (NIC), Sivasagar.
            All Rights Reserved.
        </small>

    </div>

</footer>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <!-- =========================
         AUTO HIDE ALERTS
    ========================= -->
    <script>

        setTimeout(() => {

            let alerts = document.querySelectorAll('.alert');

            alerts.forEach(alert => {

                let bsAlert =
                    bootstrap.Alert.getOrCreateInstance(alert);

                bsAlert.close();

            });

        }, 5000);

    </script>


    {{-- PAGE SCRIPTS --}}
    @yield('scripts')

</body>

</html>