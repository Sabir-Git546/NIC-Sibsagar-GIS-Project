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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="dashboard-title">
                @if(session('roleid') == 1)
                    Admin Dashboard Overview
                @else
                    User Dashboard Overview
                @endif
            </h1>
        </div>

        <!-- STATS CARDS -->
        <div class="row g-4">

            <div class="col-md-4">
                <div class="card-box text-center shadow-sm">
                    <h5 class="text-muted">Departments</h5>
                    <h1 class="fw-bold text-primary">{{ $totalDepartments }}</h1>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box text-center shadow-sm">
                    <h5 class="text-muted">Users</h5>
                    <h1 class="fw-bold text-success">{{ $totalUsers }}</h1>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box text-center shadow-sm">
                    <h5 class="text-muted">Projects</h5>
                    <h1 class="fw-bold text-dark">{{ $totalProjects }}</h1>
                </div>
            </div>

        </div>

        <!-- RECENT ACTIVITY -->
        <div class="mt-5 card-box">

            <h4 class="mb-3">Recent Activity</h4>

            <ul class="list-group">

                @forelse($recentActivity as $activity)
                <li class="list-group-item d-flex justify-content-between align-items-start">

                    <div>

                        {{-- USER --}}
                        <strong>
                            @if(session('userid') == $activity->userid)
                                You
                            @else
                                {{ $activity->username ?? 'User' }}
                            @endif
                        </strong>

                        {{-- ACTION ICON --}}
                        @if($activity->action == 'edit')
                            <span class="text-primary ms-2">✏️ Edit</span>
                        @elseif($activity->action == 'delete')
                            <span class="text-danger ms-2">🗑 Delete</span>
                        @elseif($activity->action == 'create')
                            <span class="text-success ms-2">➕ Create</span>
                        @endif

                        {{-- MODULE --}}
                        <span class="ms-1">
                            {{ ucfirst($activity->module) }}
                        </span>

                        {{-- PROJECT NAME --}}
                        @if($activity->projectname)
                            :
                            <strong>{{ $activity->projectname }}</strong>
                        @endif

                        {{-- STATUS --}}
                        @if($activity->status == 'pending')
                            <span class="badge bg-warning text-dark ms-2">Pending</span>
                        @elseif($activity->status == 'approved')
                            <span class="badge bg-success ms-2">Approved</span>
                        @else
                            <span class="badge bg-danger ms-2">Rejected</span>
                        @endif

                    </div>

                    {{-- TIME --}}
                    <small class="text-muted">
                        {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                    </small>

                </li>
                @empty
                <li class="list-group-item text-center">
                    No recent activity
                </li>
                @endforelse

            </ul>

        </div>

    </div>

</div>

@endsection