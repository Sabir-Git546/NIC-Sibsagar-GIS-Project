<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sibsagar Zilla Jankaare')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @yield('styles')
</head>

<body>

<!-- HEADER -->
<div class="header py-3">
    <div class="container">
        <h2>Govt. of Assam</h2>
        <h3>Sibsagar District Administration</h3>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Sibsagar Zilla Jankaare</a>
        <div>
            <a class="nav-link text-white d-inline" href="{{ url('/') }}">Home</a>
            <a class="nav-link text-white d-inline" href="{{ route('login') }}">User Login</a>
        </div>
    </div>
</nav>

<!-- CONTENT -->
<div class="content py-5">
    <div class="container">
        @yield('content')
    </div>
</div>

<!-- FOOTER -->
<div class="footer py-2 text-center">
    © 2026 Sibsagar District Administration
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
