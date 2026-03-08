<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Dashboard Custom CSS -->
    @yield('styles')

</head>
<body>

    {{-- Header (if you have one) --}}
    @include('layouts.header')

    {{-- Top Navbar (if you have one) --}}
    @include('layouts.navbar')

    {{-- Main Dashboard Content --}}
    @yield('content')

    {{-- Footer (optional) --}}
    @include('layouts.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')

</body>
</html>