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

        <!-- PAGE TITLE -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="dashboard-title">Add Administrative Unit</h1>
        </div>

        <div class="card-body">

            <!-- SUCCESS MESSAGE -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- VALIDATION ERRORS -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORM START -->
            <form action="{{ route('dist-bound.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <!-- UNIT NAME -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Name</label>
                        <input 
                            type="text" 
                            name="unitname" 
                            class="form-control" 
                            placeholder="Enter unit name"
                            value="{{ old('unitname') }}"
                            required
                        >
                    </div>

                    <!-- UNIT TYPE -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Type</label>
                        <select name="unittype" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="State">State</option>
                            <option value="District">District</option>
                            <option value="Sub-District">Sub-District</option>
                            <option value="Block">Block</option>
                            <option value="Village">Village</option>
                        </select>
                    </div>

                    <!-- PARENT UNIT -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent Unit</label>
                        <select name="parent_unitid" class="form-select">
                            <option value="">None (Top Level)</option>

                            @foreach($units as $unit)
                                <option value="{{ $unit->unitid }}">
                                    {{ $unit->unitname }} ({{ $unit->unittype }})
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- GEOMETRY FILE -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Upload Boundary</label>
                        <input 
                            type="file" 
                            name="geometry" 
                            class="form-control" 
                            accept=".geojson,.json,.zip"
                            required
                        >

                        <small class="text-muted">
                            Supported formats: GeoJSON, JSON, Zipped Shapefile
                        </small>
                    </div>

                </div>

                <!-- BUTTONS -->
                <div class="text-end">
                    <button type="reset" class="btn btn-secondary px-4">Reset</button>
                    <button type="submit" class="btn btn-primary px-4">Save Unit</button>
                </div>

            </form>
            <!-- FORM END -->

        </div>
    </div>
</div>

@endsection