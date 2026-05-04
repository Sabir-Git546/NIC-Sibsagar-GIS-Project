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

        <h1 class="dashboard-title mb-4">Permission Approvals</h1>

        <div class="card">
            <div class="card-body">

                <!-- 🔥 KEEPING YOUR EXISTING TABLE EXACTLY SAME -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($requests as $req)
                        <tr>
                            <td>{{ $req->requestid }}</td>
                            <td>{{ $req->userid }}</td>
                            <td>{{ $req->module }}</td>
                            <td>{{ $req->action }}</td>
                            <td>{{ $req->status }}</td>
                            <td>{{ $req->created_at }}</td>

                            <td>
                                @if($req->status == 'pending')

                                <form action="{{ route('approvals.approve', $req->requestid) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Approve</button>
                                </form>

                                <form action="{{ route('approvals.reject', $req->requestid) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn btn-danger btn-sm">Reject</button>
                                </form>

                                @else
                                    {{ ucfirst($req->status) }}
                                @endif
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                <!-- 🔥 END: YOUR TABLE -->

            </div>
        </div>

    </div>
</div>

@endsection