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

        <!-- HEADER CARD -->
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">

                <h3 class="mb-0">
                    User Activity Logs
                </h3>

                <span class="badge bg-dark">
                    Last 50–100 Records
                </span>

            </div>
        </div>

        <!-- SUCCESS MESSAGE -->
        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif

        <!-- ERROR MESSAGE -->
        @if(session('error'))

            <div class="alert alert-danger">
                {{ session('error') }}
            </div>

        @endif

        <!-- FILTER CARD -->
        <div class="card shadow-sm mb-3">

            <div class="card-body">

                <form method="GET" class="row g-2 align-items-end">

                    <!-- USER FILTER -->
                    <div class="col-md-3">

                        <label class="form-label">
                            User
                        </label>

                        <input type="text"
                               name="user"
                               class="form-control"
                               placeholder="User ID"
                               value="{{ request('user') }}">

                    </div>

                    <!-- MODULE FILTER -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Module
                        </label>

                        <select name="module" class="form-control">

                            <option value="">
                                All Modules
                            </option>

                            <option value="PROJECT"
                                {{ request('module')=='PROJECT'?'selected':'' }}>
                                Project
                            </option>

                            <option value="GIS"
                                {{ request('module')=='GIS'?'selected':'' }}>
                                GIS
                            </option>

                            <option value="USER"
                                {{ request('module')=='USER'?'selected':'' }}>
                                User
                            </option>

                        </select>

                    </div>

                    <!-- ACTION FILTER -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Action
                        </label>

                        <select name="action" class="form-control">

                            <option value="">
                                All Actions
                            </option>

                            @foreach([

                                'CREATE',

                                'CREATE_REQUEST',

                                'UPDATE',

                                'UPDATE_REQUEST',

                                'DELETE',

                                'DELETE_REQUEST',

                                'UPDATE_APPROVED',

                                'DELETE_APPROVED',

                                'CREATE_APPROVED',

                                'REJECTED'

                            ] as $act)

                                <option value="{{ $act }}"
                                    {{ request('action')==$act?'selected':'' }}>

                                    {{ $act }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <!-- BUTTONS -->
                    <div class="col-md-3 d-flex gap-2">

                        <button class="btn btn-primary w-100">

                            Filter

                        </button>

                        <a href="{{ route('audit.logs') }}"
                           class="btn btn-secondary w-100">

                            Reset

                        </a>

                    </div>

                </form>

            </div>

        </div>

        <!-- TABLE CARD -->
        <div class="card shadow-sm">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-striped mb-0">

                        <thead class="table-dark">

                            <tr>

                                <th>ID</th>

                                <th>User</th>

                                <th>Module</th>

                                <th>Action</th>

                                <th>Record</th>

                                <th>IP</th>

                                <th>Before</th>

                                <th>After</th>

                                <th>Date</th>

                            </tr>

                        </thead>

                        <tbody>

                        @forelse($logs as $log)

                            <tr>

                                <!-- ID -->
                                <td>
                                    {{ $log->id }}
                                </td>

                                <!-- USER -->
                                <td>

                                    <span class="badge bg-info text-dark">

                                        {{ $log->userid ?? '-' }}

                                    </span>

                                </td>

                                <!-- MODULE -->
                                <td>

                                    <span class="fw-semibold">

                                        {{ strtoupper($log->module) }}

                                    </span>

                                </td>

                                <!-- ACTION -->
                                <td>

                                    @php

                                        $color = match($log->action) {

                                            'CREATE' => 'success',

                                            'CREATE_REQUEST' => 'warning',

                                            'UPDATE' => 'primary',

                                            'UPDATE_REQUEST' => 'warning',

                                            'DELETE' => 'danger',

                                            'DELETE_REQUEST' => 'danger',

                                            'UPDATE_APPROVED' => 'info',

                                            'DELETE_APPROVED' => 'dark',

                                            'CREATE_APPROVED' => 'success',

                                            'REJECTED' => 'secondary',

                                            default => 'light'
                                        };

                                    @endphp

                                    <span class="badge bg-{{ $color }}">

                                        {{ $log->action }}

                                    </span>

                                </td>

                                <!-- RECORD -->
                                <td>

                                    {{ $log->recordid ?? '-' }}

                                </td>

                                <!-- IP -->
                                <td class="text-muted small">

                                    {{ $log->ip_address ?? 'N/A' }}

                                </td>

                                <!-- OLD DATA -->
                                <td>

                                    @if($log->old_data)

                                        <button class="btn btn-sm btn-outline-secondary"
                                            onclick='showJson(@json(json_decode($log->old_data ?? "{}")))'>

                                            View

                                        </button>

                                    @else

                                        -

                                    @endif

                                </td>

                                <!-- NEW DATA -->
                                <td>

                                    @if($log->new_data)

                                        <button class="btn btn-sm btn-outline-success"
                                            onclick='showJson(@json(json_decode($log->new_data ?? "{}")))'>

                                            View

                                        </button>

                                    @else

                                        -

                                    @endif

                                </td>

                                <!-- DATE -->
                                <td class="text-nowrap">

                                    {{ $log->created_at }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="text-center py-4">

                                    <span class="text-muted">

                                        No audit logs found

                                    </span>

                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <!-- PAGINATION -->
        <div class="mt-3">

            {{ $logs->withQueryString()->links() }}

        </div>

    </div>
</div>

<!-- MODAL OVERLAY -->
<div id="modalOverlay" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    z-index:9998;
"
onclick="closeJsonModal()">
</div>

<!-- JSON MODAL -->
<div id="jsonModal" style="
    display:none;
    position:fixed;
    top:10%;
    left:20%;
    width:60%;
    background:white;
    border-radius:10px;
    padding:20px;
    z-index:9999;
    box-shadow:0 0 20px rgba(0,0,0,0.3);
">

    <pre id="jsonContent"
         style="
            max-height:400px;
            overflow:auto;
            background:#f8f9fa;
            padding:15px;
            border-radius:8px;
         ">
    </pre>

    <div class="text-end mt-2">

        <button class="btn btn-danger btn-sm"
            onclick="closeJsonModal()">

            Close

        </button>

    </div>

</div>

<script>

function showJson(data) {

    document.getElementById('jsonContent').textContent =

        JSON.stringify(data, null, 2);

    document.getElementById('jsonModal').style.display = 'block';

    document.getElementById('modalOverlay').style.display = 'block';
}

function closeJsonModal() {

    document.getElementById('jsonModal').style.display = 'none';

    document.getElementById('modalOverlay').style.display = 'none';
}

</script>

@endsection