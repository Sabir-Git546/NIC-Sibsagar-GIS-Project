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

     <div class="main-content">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Department</h5>
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

                    <form action="{{ route('department.update', $department->deptid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Department ID (Readonly) --}}
                        <div class="mb-3">
                            <label class="form-label">Department ID</label>
                            <input type="text"
                                class="form-control"
                                value="{{ $department->deptid }}"
                                readonly>
                        </div>

                        {{-- Department Name --}}
                        <div class="mb-3">
                            <label class="form-label">Department Name</label>
                            <input type="text"
                                name="deptname"
                                class="form-control"
                                value="{{ old('deptname', $department->deptname) }}"
                                required>
                        </div>

                        {{-- Department Description --}}
                        <div class="mb-3">
                            <label class="form-label">Department Description</label>
                            <textarea name="deptdescription"
                                    class="form-control"
                                    rows="3">{{ old('deptdescription', $department->deptdescription) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('department.index') }}" class="btn btn-secondary">
                                Back
                            </a>

                            <button type="submit" class="btn btn-success">
                                Update Department
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>    

</div>

@endsection