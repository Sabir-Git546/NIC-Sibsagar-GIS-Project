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

    @include('layouts.left-nav')

    <div class="main-content">

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">

                    @if(auth()->user()->roleid == 1)
                        Edit Project
                    @else
                        Request Project Update
                    @endif

                </h5>

            </div>

            <div class="card-body">

                @if($errors->any())

                    <div class="alert alert-danger">

                        <ul class="mb-0">

                            @foreach($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                @endif

                <form action="{{ route('projects.update', $project->projectid) }}"
                      method="POST"
                      id="editProjectForm">

                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label class="form-label">
                            Project ID
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $project->projectid }}"
                               readonly>

                    </div>

                    <div class="row mb-3">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Project Name
                            </label>

                            <input type="text"
                                   name="projectname"
                                   class="form-control"
                                   value="{{ old('projectname', $project->projectname) }}"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status"
                                    class="form-select"
                                    required>

                                <option value="planning"
                                    {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>
                                    Planning
                                </option>

                                <option value="ongoing"
                                    {{ old('status', $project->status) == 'ongoing' ? 'selected' : '' }}>
                                    Ongoing
                                </option>

                                <option value="completed"
                                    {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Department
                            </label>

                            <select name="deptid"
                                    class="form-select"
                                    required>

                                @foreach($departments as $dept)

                                    <option value="{{ $dept->deptid }}"
                                        {{ old('deptid', $project->deptid) == $dept->deptid ? 'selected' : '' }}>

                                        {{ $dept->deptname }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Location
                            </label>

                            <select name="location_unitid"
                                    class="form-select"
                                    required>

                                @foreach($units as $unit)

                                    <option value="{{ $unit->unitid }}"
                                        {{ old('location_unitid', $project->location_unitid) == $unit->unitid ? 'selected' : '' }}>

                                        {{ $unit->unitname }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description"
                                  class="form-control"
                                  rows="4">{{ old('description', $project->description) }}</textarea>

                    </div>

                    <div class="d-flex justify-content-between">

                        <a href="{{ route('projects.index') }}"
                           class="btn btn-secondary">

                            Back

                        </a>

                        <button type="submit"
                                class="btn btn-success"
                                id="submitBtn"
                                data-role="{{ auth()->user()->roleid == 1 ? 'admin' : 'user' }}"
                                onclick="return confirmUpdate('{{ auth()->user()->roleid == 1 ? 'admin' : 'user' }}')">

                            @if(auth()->user()->roleid == 1)
                                Update Project
                            @else
                                Send Update Request
                            @endif

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