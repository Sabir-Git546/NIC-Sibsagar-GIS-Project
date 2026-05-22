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

    <!-- SIDEBAR -->
    @include('layouts.left-nav')

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <h1 class="dashboard-title">
                Department Projects View
            </h1>

        </div>

        <!-- CARD -->
        <div class="card shadow-sm">

            <div class="card-body">

                <!-- SUCCESS -->
                @if(session('success'))

                    <div class="alert alert-success alert-dismissible fade show">

                        {{ session('success') }}

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                        </button>

                    </div>

                @endif

                <!-- ERROR -->
                @if(session('error'))

                    <div class="alert alert-danger alert-dismissible fade show">

                        {{ session('error') }}

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                        </button>

                    </div>

                @endif

                <!-- TABLE -->
                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover">

                        <thead class="table-dark">

                            <tr>

                                <th>ID</th>

                                <th>Project Name</th>

                                <th>Status</th>

                                <th>Department</th>

                                <th>Location</th>

                                <th>Description</th>

                                <th>Created By</th>

                                <th>Created At</th>

                                <th width="220">
                                    Actions
                                </th>

                                <th width="140">
                                    GIS & Documents
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        @forelse($projects as $prj)

                        @php

                            // CHECK PENDING REQUEST
                            $pendingRequest = DB::table('approval_requests')

                                ->where('module', 'PROJECT')

                                ->where('recordid', $prj->projectid)

                                ->where('status', 'pending')

                                ->exists();

                        @endphp

                        <tr>

                            <!-- ID -->
                            <td>

                                {{ $prj->projectid }}

                            </td>

                            <!-- NAME -->
                            <td>

                                {{ $prj->projectname }}

                            </td>

                            <!-- STATUS -->
                            <td>

                                @switch($prj->status)

                                    @case('planning')

                                        <span class="badge bg-warning">

                                            Planning

                                        </span>

                                        @break

                                    @case('ongoing')

                                        <span class="badge bg-primary">

                                            Ongoing

                                        </span>

                                        @break

                                    @case('completed')

                                        <span class="badge bg-success">

                                            Completed

                                        </span>

                                        @break

                                    @default

                                        <span class="badge bg-secondary">

                                            {{ $prj->status }}

                                        </span>

                                @endswitch

                            </td>

                            <!-- DEPARTMENT -->
                            <td>

                                {{ $prj->department->deptname ?? '-' }}

                            </td>

                            <!-- LOCATION -->
                            <td>

                                {{ $prj->locationUnit->unitname ?? '-' }}

                            </td>

                            <!-- DESCRIPTION -->
                            <td>

                                {{ Str::limit($prj->description ?? '-', 80) }}

                            </td>

                            <!-- CREATED BY -->
                            <td>

                                {{ $prj->createdby }}

                            </td>

                            <!-- CREATED AT -->
                            <td>

                                {{ $prj->createdat }}

                            </td>

                            <!-- ACTIONS -->
                            <td class="text-center">

                                <div class="d-flex justify-content-center gap-2 flex-wrap">

                                    <!-- ADMIN -->
                                    @if(auth()->user()->roleid == 1)

                                        <!-- DIRECT UPDATE -->
                                        <a href="{{ route('projects.edit', $prj->projectid) }}"
                                           class="btn btn-sm btn-primary">

                                            Update

                                        </a>

                                        <!-- DIRECT DELETE -->
                                        <form action="{{ route('projects.destroy', $prj->projectid) }}"
                                              method="POST"
                                              class="m-0 p-0">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirmDelete('admin')">

                                                Delete

                                            </button>

                                        </form>

                                    <!-- NORMAL USER -->
                                    @else

                                        <!-- PENDING -->
                                        @if($pendingRequest)

                                            <button class="btn btn-sm btn-warning"
                                                    disabled>

                                                Pending Approval

                                            </button>

                                        @else

                                            <!-- REQUEST UPDATE -->
                                            <a href="{{ route('projects.edit', $prj->projectid) }}"
                                               class="btn btn-sm btn-primary">

                                                Request Update

                                            </a>

                                            <!-- REQUEST DELETE -->
                                            <form action="{{ route('projects.destroy', $prj->projectid) }}"
                                                  method="POST"
                                                  class="m-0 p-0">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirmDelete('user')">

                                                    Request Delete

                                                </button>

                                            </form>

                                        @endif

                                    @endif

                                </div>

                            </td>

                            <!-- GIS & DOCS -->
                            <td class="text-center">

                                <div class="d-flex justify-content-center gap-2">

                                    <!-- GIS -->
                                    <a href="{{ route('gis.view', $prj->projectid) }}"
                                       class="btn btn-sm btn-warning">

                                        GIS

                                    </a>

                                    <!-- DOCS -->
                                    <a href="#"
                                       class="btn btn-sm btn-secondary">

                                        Docs

                                    </a>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="10"
                                class="text-center py-4">

                                <span class="text-muted">

                                    No Projects Found

                                </span>

                            </td>

                        </tr>

                        @endforelse

                        </tbody>

                    </table>

                    {{-- PAGINATION --}}
                    @if(method_exists($projects, 'links') && $projects->hasPages())

                        <div class="mt-4 d-flex justify-content-center">

                            {{ $projects->links() }}

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endauth

@endsection

@section('scripts')
<script src="{{ asset('js/project-module.js') }}"></script>
@endsection