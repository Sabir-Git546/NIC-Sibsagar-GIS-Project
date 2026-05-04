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

        <h1 class="dashboard-title mb-4">Audit Trail Logs</h1>

        <!-- FILTER BAR -->
        <form method="GET" class="mb-3 d-flex gap-2">

            <input type="text" name="user" class="form-control" placeholder="User ID">

            <select name="module" class="form-control">
                <option value="">All Modules</option>
                <option value="project">Project</option>
                <option value="gis">GIS</option>
                <option value="user">User</option>
            </select>

            <select name="action" class="form-control">
                <option value="">All Actions</option>
                <option value="create">Create</option>
                <option value="update_request">Update Request</option>
                <option value="delete_request">Delete Request</option>
                <option value="update_approved">Update Approved</option>
                <option value="delete_approved">Delete Approved</option>
                <option value="rejected">Rejected</option>
            </select>

            <button class="btn btn-primary">Filter</button>
        </form>

        <!-- TABLE -->
        <div class="card">
            <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Record ID</th>
                            <th>IP Address</th>
                            <th>Before</th>
                            <th>After</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($logs as $log)
                        <tr>

                            <td>{{ $log->id }}</td>

                            <td>{{ $log->userid ?? '-' }}</td>

                            <td>{{ ucfirst($log->module) }}</td>

                            <!-- ACTION BADGE -->
                            <td>
                                @php
                                    $color = match($log->action) {
                                        'create' => 'success',
                                        'update_request' => 'warning',
                                        'delete_request' => 'danger',
                                        'update_approved' => 'primary',
                                        'delete_approved' => 'dark',
                                        'rejected' => 'secondary',
                                        default => 'info'
                                    };
                                @endphp

                                <span class="badge bg-{{ $color }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td>{{ $log->recordid ?? '-' }}</td>

                            <!-- IP ADDRESS -->
                            <td>
                                <small class="text-muted">
                                    {{ $log->ip_address ?? 'N/A' }}
                                </small>
                            </td>

                            <!-- BEFORE -->
                            <td>
                                @if($log->old_data)
                                <button class="btn btn-sm btn-secondary"
                                    onclick="showJson(@json(json_decode($log->old_data)))">
                                    View
                                </button>
                                @else
                                -
                                @endif
                            </td>

                            <!-- AFTER -->
                            <td>
                                @if($log->new_data)
                                <button class="btn btn-sm btn-success"
                                    onclick="showJson(@json(json_decode($log->new_data)))">
                                    View
                                </button>
                                @else
                                -
                                @endif
                            </td>

                            <td>
                                {{ $log->created_at }}
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                No audit logs found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>
        </div>

    </div>
</div>

<!-- JSON MODAL -->
<div id="jsonModal" style="
    display:none;
    position:fixed;
    top:10%;
    left:20%;
    width:60%;
    background:white;
    border:1px solid #ccc;
    padding:20px;
    z-index:9999;
    box-shadow:0 0 10px rgba(0,0,0,0.3);
">

    <pre id="jsonContent" style="max-height:400px; overflow:auto;"></pre>

    <button class="btn btn-danger btn-sm"
        onclick="document.getElementById('jsonModal').style.display='none'">
        Close
    </button>
</div>

<script>
function showJson(data) {
    document.getElementById('jsonContent').textContent =
        JSON.stringify(data, null, 2);
    document.getElementById('jsonModal').style.display = 'block';
}
</script>

@endsection