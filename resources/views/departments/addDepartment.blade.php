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
            <h1 class="dashboard-title">Add Department</h1>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('department.store') }}" method="POST">
            @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Department Name</label>
                        <input type="text" name="deptname" class="form-control" placeholder="Enter department name" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Department Code</label>
                        <input type="text" name="deptcode" class="form-control" placeholder="Enter department code">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="deptdescription" class="form-control" rows="3" placeholder="Enter department description"></textarea>
                </div>


                <div class="text-end">
                    <button type="reset" class="btn btn-secondary px-4">Reset</button>
                    <button type="submit" class="btn btn-primary px-4">Save Department</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection