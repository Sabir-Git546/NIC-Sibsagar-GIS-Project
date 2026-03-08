@php 
    $hideNavbar = true; 
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
@endsection

@section('content')

<div class="dashboard-container">

    <!-- SIDEBAR -->
    @include('layouts.left-nav')

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Edit Project</h5>
            </div>

            <div class="card-body">

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('projects.update', $project->projectid) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Project ID (Readonly) --}}
                    <div class="mb-3">
                        <label class="form-label">Project ID</label>
                        <input type="text" class="form-control" value="{{ $project->projectid }}" readonly>
                    </div>

                    <div class="row mb-3">

                        {{-- Project Name --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Project Name</label>
                            <input type="text"
                                name="projectname"
                                class="form-control"
                                value="{{ old('projectname', $project->projectname) }}"
                                required>
                        </div>

                        {{-- Project Status --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="">Select Status</option>
                                <option value="Planning" {{ old('status', $project->status) == 'Planning' ? 'selected' : '' }}>Planning</option>
                                <option value="Ongoing" {{ old('status', $project->status) == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="Completed" {{ old('status', $project->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        {{-- Department --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department</label>
                            <select name="deptid" class="form-select" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->deptid }}" {{ old('deptid', $project->deptid) == $dept->deptid ? 'selected' : '' }}>
                                        {{ $dept->deptname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Location --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location (Village/Block)</label>
                            <select name="location_unitid" class="form-select" required>
                                <option value="">Select Location</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->unitid }}" {{ old('location_unitid', $project->location_unitid) == $unit->unitid ? 'selected' : '' }}>
                                        {{ $unit->unitname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- Project Description --}}
                    <div class="mb-3">
                        <label class="form-label">Project Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                            Back
                        </a>

                        <button type="submit" class="btn btn-success">
                            Update Project
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>    

</div>

@endsection