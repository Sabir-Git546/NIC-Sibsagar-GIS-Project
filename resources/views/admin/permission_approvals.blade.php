@php 
    $hideNavbar = true; 
@endphp

@extends('layouts.master')

@section('styles')

<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">

@endsection


@section('content')

<div class="dashboard-container">

    {{-- SIDEBAR --}}
    @include('layouts.left-nav')


    {{-- MAIN CONTENT --}}
    <div class="main-content">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <h1 class="dashboard-title">
                Permission Approvals
            </h1>

        </div>


        <!-- CARD -->
        <div class="card shadow-sm">

            <div class="card-body">

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

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

                                <th width="200">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($requests as $req)

                                <tr>

                                    <!-- REQUEST ID -->
                                    <td>
                                        {{ $req->requestid }}
                                    </td>


                                    <!-- USER -->
                                    <td>
                                        {{ $req->userid }}
                                    </td>


                                    <!-- MODULE -->
                                    <td>
                                        {{ $req->module }}
                                    </td>


                                    <!-- ACTION -->
                                    <td>
                                        {{ $req->action }}
                                    </td>


                                    <!-- RECORD ID -->
                                    <td>
                                        {{ $req->recordid }}
                                    </td>


                                    <!-- LAYER -->
                                    <td>
                                        {{ $req->layername ?? '-' }}
                                    </td>


                                    <!-- STATUS -->
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


                                    <!-- CREATED -->
                                    <td>
                                        {{ $req->created_at }}
                                    </td>


                                    <!-- APPROVED BY -->
                                    <td>
                                        {{ $req->approved_by ?? '-' }}
                                    </td>


                                    <!-- APPROVED AT -->
                                    <td>
                                        {{ $req->approved_at ?? '-' }}
                                    </td>


                                    <!-- ACTION BUTTONS -->
                                    <td>

                                        @if($req->status == 'pending')

                                            <!-- APPROVE -->
                                            <form action="{{ route('approvals.approve', $req->requestid) }}"
                                                  method="POST"
                                                  class="d-inline">

                                                @csrf

                                                <button type="submit"
                                                        class="btn btn-success btn-sm"
                                                        onclick="return confirm('Approve this request?')">

                                                    Approve

                                                </button>

                                            </form>


                                            <!-- REJECT -->
                                            <form action="{{ route('approvals.reject', $req->requestid) }}"
                                                  method="POST"
                                                  class="d-inline">

                                                @csrf

                                                <button type="submit"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Reject this request?')">

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

                                    <td colspan="11"
                                        class="text-center text-muted py-4">

                                        No approval requests found.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                <!-- PAGINATION -->
                @if(method_exists($requests, 'links') && $requests->hasPages())

                    <div class="d-flex justify-content-center mt-4">

                        {{ $requests->links() }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection