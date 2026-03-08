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
            <h1 class="dashboard-title">Add Project</h1>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="projectname" class="form-control" placeholder="Enter project name" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="">Select Status</option>
                            <option value="Planning">Planning</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department</label>
                        <select name="deptid" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->deptid }}">{{ $dept->deptname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Location (Village/Block)</label>
                        <select name="location_unitid" class="form-select" required>
                            <option value="">Select Location</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->unitid }}">{{ $unit->unitname }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mb-3">
                    <label class="form-label">Project Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Enter project description"></textarea>
                </div>


                <input type="hidden" name="createdby" value="{{ session('userid') }}">

                <div class="text-end">
                    <button type="reset" class="btn btn-secondary px-4">Reset</button>
                    <button type="submit" class="btn btn-primary px-4">Save Project</button>
                </div>

            </form>

        </div>
    </div>
</div>



@endsection