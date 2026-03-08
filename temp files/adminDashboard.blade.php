@php $hideNavbar = true; @endphp
@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
@endsection


@section('content')

<div class="dashboard-container">

    <!-- ================= SIDEBAR ================= -->
    <div class="sidebar">

        <div class="menu-title">
            @if(auth()->user()->roleid == 1)
                Admin Panel
            @else
                User Panel
            @endif
        </div>

        <!-- Project Management (Visible To All) -->
        <a class="menu-link" data-bs-toggle="collapse" href="#projectMenu">
            Project / Scheme Management
        </a>
        <div class="collapse submenu" id="projectMenu">
            <a href="#" class="submenu-link">Add Project</a>
            <a href="#" class="submenu-link">View Projects</a>
            <a href="#" class="submenu-link">Update Status</a>
        </div>

        <!-- GIS App (Visible To All) -->
        <a class="menu-link" data-bs-toggle="collapse" href="#gisMenu">
            GIS App
        </a>
        <div class="collapse submenu" id="gisMenu">
            <a href="#" class="submenu-link">View Map</a>
            <a href="#" class="submenu-link">Layer Control</a>
        </div>

        <!-- Reports (Visible To All) -->
        <a class="menu-link" data-bs-toggle="collapse" href="#reportMenu">
            Report
        </a>
        <div class="collapse submenu" id="reportMenu">
            <a href="#" class="submenu-link">Generate Report</a>
            <a href="#" class="submenu-link">Download Reports</a>
        </div>

        <!-- ================= ADMIN ONLY ================= -->
        @if(auth()->user()->roleid == 1)

        <!-- Department Management -->
        <a class="menu-link" data-bs-toggle="collapse" href="#deptMenu">
            Department Management
        </a>
        <div class="collapse submenu" id="deptMenu">
            <a href="{{ url('addDepartment') }}" class="submenu-link">Add Department</a>
            <a href="#" class="submenu-link">View Departments</a>
        </div>

        <!-- User Management -->
        <a class="menu-link" data-bs-toggle="collapse" href="#userMenu">
            User Management
        </a>
        <div class="collapse submenu" id="userMenu">
            <a href="#" class="submenu-link">Add User</a>
            <a href="#" class="submenu-link">User List</a>
            <a href="#" class="submenu-link">Assign Roles</a>
        </div>

        @endif
        <!-- ============================================= -->

    </div>
    <!-- ================================================= -->



    <!-- ================= MAIN CONTENT ================= -->
    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="dashboard-title">
                @if(auth()->user()->roleid == 1)
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
            <p>Recent system actions will appear here...</p>
        </div>

    </div>
    <!-- ================================================= -->

</div>

@endsection