@php $hideNavbar = true; @endphp
@extends('layouts.master')

@section('header-right')
    <span class="me-3 text-white">Welcome, User</span>
    <a href="#" class="btn btn-light btn-sm">Logout</a>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
@endsection

@section('content')

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <div class="sidebar">

    <div class="menu-title">User Panel</div>

    <!-- Project Management -->
    <a class="menu-link" data-bs-toggle="collapse" href="#projectMenu" role="button">
        Project / Scheme Management
    </a>
    <div class="collapse submenu" id="projectMenu">
        <a href="#" class="submenu-link">Add Project</a>
        <a href="#" class="submenu-link">View Projects</a>
        <a href="#" class="submenu-link">Update Status</a>
    </div>

    <!-- GIS App -->
    <a class="menu-link" data-bs-toggle="collapse" href="#gisMenu" role="button">
        GIS App
    </a>
    <div class="collapse submenu" id="gisMenu">
        <a href="#" class="submenu-link">View Map</a>
        <a href="#" class="submenu-link">Layer Control</a>
    </div>

    <!-- Reports -->
    <a class="menu-link" data-bs-toggle="collapse" href="#reportMenu" role="button">
        Report
    </a>
    <div class="collapse submenu" id="reportMenu">
        <a href="#" class="submenu-link">Generate Report</a>
        <a href="#" class="submenu-link">Download Reports</a>
    </div>

</div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="dashboard-title">Dashboard Overview</h1>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card-box text-center">
                    <h4>Total Departments</h4>
                    <h1>12</h1>
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
            <p>Recent administrative actions will appear here...</p>
        </div>

    </div>

</div>

@endsection