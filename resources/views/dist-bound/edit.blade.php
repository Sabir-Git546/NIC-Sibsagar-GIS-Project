@php 
    $hideNavbar = true; 
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
@endsection

@section('content')

<div class="dashboard-container">

    @include('layouts.left-nav')

    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="dashboard-title">Edit Administrative Unit</h1>
        </div>

        <div class="card-body">

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- ERRORS --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dist-bound.update', $unit->unitid) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">

                    <!-- UNIT NAME -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Name</label>
                        <input type="text" name="unitname" class="form-control"
                               value="{{ old('unitname', $unit->unitname) }}" required>
                    </div>

                    <!-- UNIT TYPE -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Type</label>
                        <select name="unittype" class="form-select" required>
                            @foreach(['State','District','Sub-District','Block','Village'] as $type)
                                <option value="{{ $type }}" {{ $unit->unittype == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PARENT UNIT -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent Unit</label>
                        <select name="parent_unitid" class="form-select">
                            <option value="">None</option>
                            @foreach($units as $u)
                                <option value="{{ $u->unitid }}"
                                    {{ $unit->parent_unitid == $u->unitid ? 'selected' : '' }}>
                                    {{ $u->unitname }} ({{ $u->unittype }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- GEOMETRY -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Replace Boundary File (optional)</label>
                        <input type="file" name="geometry" class="form-control" accept=".geojson,.json,.zip">

                        @if($unit->geometry)
                            <small class="text-muted">Existing file already uploaded</small>
                        @endif
                    </div>

                </div>

                <div class="text-end">
                    <a href="{{ route('dist-bound.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection