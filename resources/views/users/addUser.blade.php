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

    <!-- SIDEBAR -->
    
    @include('layouts.left-nav')

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="dashboard-title">Add User</h1>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('user.store') }}" method="POST">
            @csrf
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">User Id</label>
                        <input type="text" name="userid" class="form-control" placeholder="Enter user id" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">User Name</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter user name" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">User Password</label>
                        <input type="password" name="userpass" class="form-control" placeholder="Enter user password">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">User Email</label>
                        <input type="email" name="useremail" class="form-control" placeholder="Enter user email" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">User Address</label>
                        <input type="text" name="useraddress" class="form-control" placeholder="Enter user address" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">User Phone</label>
                        <input type="number" name="userphno" class="form-control" placeholder="Enter user phone no." required>
                    </div>

                    <!--Select department from table departments-->
                    <div class="col-md-6">
                        <label class="form-label">Department</label>
                        <select name="deptid" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach($depts as $dept)
                                <option value="{{ $dept->deptid }}">
                                    {{ $dept->deptname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!--Select user from table users-->
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select name="roleid" class="form-select" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->roleid }}">
                                    {{ $role->rolename }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="text-end">
                    <button type="reset" class="btn btn-secondary px-4">Reset</button>
                    <button type="submit" class="btn btn-primary px-4">Save User</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection