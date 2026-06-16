@php 
    $hideNavbar = true; 
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
@endsection

@section('content')

@auth

<div class="dashboard-container">

    <!-- SIDEBAR -->
    @include('layouts.left-nav')

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <h1 class="dashboard-title">
                Add Project
            </h1>

        </div>

        <!-- CARD -->
        <div class="card shadow-sm">

            <div class="card-body">

                <!-- SUCCESS 
                @if(session('success'))

                    <div class="alert alert-success alert-dismissible fade show" role="alert">

                        {{ session('success') }}

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                        </button>

                    </div>

                @endif  -->

                <!-- ERROR 
                @if(session('error'))

                    <div class="alert alert-danger alert-dismissible fade show" role="alert">

                        {{ session('error') }}

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                        </button>

                    </div>

                @endif  -->

                <!-- VALIDATION ERRORS -->
                @if($errors->any())

                    <div class="alert alert-danger">

                        <strong>
                            Please fix the following errors:
                        </strong>

                        <ul class="mb-0 mt-2">

                            @foreach($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                @endif

                <!-- FORM -->
                <form action="{{ route('projects.store') }}"
                      method="POST"
                      id="projectForm">

                    @csrf

                    <div class="row mb-3">

                        <!-- PROJECT NAME -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Project Name
                            </label>

                            <input type="text" 
                                   name="projectname" 
                                   class="form-control @error('projectname') is-invalid @enderror"
                                   value="{{ old('projectname') }}"
                                   placeholder="Enter project name" 
                                   maxlength="200"
                                   required>

                            @error('projectname')

                                <small class="text-danger">

                                    {{ $message }}

                                </small>

                            @enderror

                        </div>

                        <!-- STATUS -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>

                                <option value="">
                                    Select Status
                                </option>

                                <option value="planning"
                                    {{ old('status') == 'planning' ? 'selected' : '' }}>

                                    Planning

                                </option>

                                <option value="ongoing"
                                    {{ old('status') == 'ongoing' ? 'selected' : '' }}>

                                    Ongoing

                                </option>

                                <option value="completed"
                                    {{ old('status') == 'completed' ? 'selected' : '' }}>

                                    Completed

                                </option>

                            </select>

                            @error('status')

                                <small class="text-danger">

                                    {{ $message }}

                                </small>

                            @enderror

                        </div>

                        <!-- DEPARTMENT -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Department
                            </label>

                            <select name="deptid"
                                    class="form-select @error('deptid') is-invalid @enderror"
                                    required>

                                <option value="">
                                    Select Department
                                </option>

                                @foreach($departments as $dept)

                                    <option value="{{ $dept->deptid }}"
                                        {{ old('deptid') == $dept->deptid ? 'selected' : '' }}>

                                        {{ $dept->deptname }}

                                    </option>

                                @endforeach

                            </select>

                            @error('deptid')

                                <small class="text-danger">

                                    {{ $message }}

                                </small>

                            @enderror

                        </div>

                        <!-- LOCATION -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Location (Village/Block)
                            </label>

                            <select name="location_unitid"
                                    class="form-select @error('location_unitid') is-invalid @enderror"
                                    required>

                                <option value="">
                                    Select Location
                                </option>

                                @foreach($units as $unit)

                                    <option value="{{ $unit->unitid }}"
                                        {{ old('location_unitid') == $unit->unitid ? 'selected' : '' }}>

                                        {{ $unit->unitname }}

                                    </option>

                                @endforeach

                            </select>

                            @error('location_unitid')

                                <small class="text-danger">

                                    {{ $message }}

                                </small>

                            @enderror

                        </div>

                    </div>

                    <!-- DESCRIPTION -->
                    <div class="mb-3">

                        <label class="form-label">
                            Project Description
                        </label>

                        <textarea name="description" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="4"
                                  maxlength="1000"
                                  placeholder="Enter project description">{{ old('description') }}</textarea>

                        @error('description')

                            <small class="text-danger">

                                {{ $message }}

                            </small>

                        @enderror

                    </div>

                    <!-- BUTTONS -->
                    <div class="text-end">

                        <button type="reset"
                                class="btn btn-secondary px-4">

                            Reset

                        </button>

                        <button type="submit"
                                class="btn btn-primary px-4"
                                id="submitBtn">

                            Save Project

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endauth

@endsection

@section('scripts')
    <script src="{{ asset('js/project-module.js') }}"></script>
@endsection