@php 
    $hideNavbar = true; 
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
@endsection

@section('content')

@if(!session()->has('userid'))
    <script>
        window.location = "{{ route('login') }}";
    </script>
@endif


<div class="dashboard-container">

    <!--left side navbar-->
    
        @include('layouts.left-nav')

    <!-- ================= MAIN CONTENT ================= -->
    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="dashboard-title">
                @if(session('roleid') == 1)
                    Admin Dashboard Overview
                @else
                    User Dashboard Overview
                @endif
            </h1>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card-box text-center">
                    <h4>Total Departments</h4>
                    <h1>3</h1>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box text-center">
                    <h4>Total Users</h4>
                    <h1>85</h1>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box text-center">
                    <h4>Active Projects</h4>
                    <h1>27</h1>
                </div>
            </div>

        </div>

        <div class="mt-5 card-box">
            <h4>Recent Activity</h4>
            <p>Recent system activities will appear here...</p>
        </div>

    </div>
    <!-- ================================================= -->

</div>

@endsection