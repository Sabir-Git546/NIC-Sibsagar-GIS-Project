@php 
    $hideNavbar = true; 
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">

<style>

    .status-pending{
        color: orange;
        font-weight: bold;
    }

    .status-approved{
        color: green;
        font-weight: bold;
    }

    .status-rejected{
        color: red;
        font-weight: bold;
    }

</style>
@endsection

@section('content')

<div class="dashboard-container">

    <!-- SIDEBAR -->
    @include('layouts.left-nav')

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <h1 class="dashboard-title mb-4">
            Permission Approvals
        </h1>

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

        <div class="card">

            <div class="card-body">

                <table class="table table-bordered table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Record ID</th>
                            <th>Layer</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Approved By</th>
                            <th>Approved At</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($requests as $req)

                        <tr>

                            <td>
                                {{ $req->requestid }}
                            </td>

                            <td>
                                {{ $req->userid }}
                            </td>

                            <td>
                                {{ $req->module }}
                            </td>

                            <td>
                                {{ $req->action }}
                            </td>

                            <td>
                                {{ $req->recordid }}
                            </td>

                            <td>
                                {{ $req->layername ?? '-' }}
                            </td>

                            <td>

                                @if($req->status == 'pending')

                                    <span class="status-pending">
                                        Pending
                                    </span>

                                @elseif($req->status == 'approved')

                                    <span class="status-approved">
                                        Approved
                                    </span>

                                @else

                                    <span class="status-rejected">
                                        Rejected
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $req->created_at }}
                            </td>

                            <td>
                                {{ $req->approved_by ?? '-' }}
                            </td>

                            <td>
                                {{ $req->approved_at ?? '-' }}
                            </td>

                            <td>

                                @if($req->status == 'pending')

                                <!-- APPROVE -->

                                <form 
                                    action="{{ route('approvals.approve', $req->requestid) }}" 
                                    method="POST" 
                                    style="display:inline;"
                                    onsubmit="return confirm('Approve this request?')"
                                >

                                    @csrf

                                    <button class="btn btn-success btn-sm">

                                        Approve

                                    </button>

                                </form>

                                <!-- REJECT -->

                                <form 
                                    action="{{ route('approvals.reject', $req->requestid) }}" 
                                    method="POST" 
                                    style="display:inline;"
                                    onsubmit="return confirm('Reject this request?')"
                                >

                                    @csrf

                                    <button class="btn btn-danger btn-sm">

                                        Reject

                                    </button>

                                </form>

                                @else

                                    <span class="text-muted">

                                        Completed

                                    </span>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="11" class="text-center">

                                No approval requests found.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection