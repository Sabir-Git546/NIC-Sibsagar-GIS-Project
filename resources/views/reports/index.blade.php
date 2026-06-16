@php
    $hideNavbar = true;
@endphp

@extends('layouts.master')

@section('title', 'Reports')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">

<!-- Bootstrap Icons -->
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection

@section('content')

<div class="dashboard-container">

    @include('layouts.left-nav')

    <div class="main-content">

        <div class="container-fluid py-3">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h3 class="mb-1">Project Reports</h3>

                    <p class="text-muted mb-0">
                        Generate and export project related reports
                    </p>

                </div>

            </div>

            {{-- FILTER CARD --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white">
                    <h5 class="mb-0">Report Filters</h5>
                </div>

                <div class="card-body">

                    <form method="GET" action="{{ route('reports.projects') }}">

                        <div class="row g-3">

                            {{-- REPORT TYPE --}}
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Report Type</label>

                                <select name="report_type" class="form-select">

                                    <option value="master">Master Project Report</option>
                                    <option value="status">Status Summary</option>
                                    <option value="department">Department Wise</option>
                                    <!--<option value="gis">GIS Summary</option>
                                    <option value="geometry">Geometry Summary</option>-->

                                </select>
                            </div>

                            {{-- DEPARTMENT --}}
                            <div class="col-md-3">

                                <label class="form-label fw-semibold">Department</label>

                                <select name="deptid" class="form-select">

                                    <option value="">All Departments</option>

                                    @foreach($departments as $department)
                                        <option value="{{ $department->deptid }}">
                                            {{ $department->deptname }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            {{-- STATUS --}}
                            <div class="col-md-2">

                                <label class="form-label fw-semibold">Status</label>

                                <select name="status" class="form-select">

                                    <option value="">All</option>
                                    <option value="planning">Planning</option>
                                    <option value="Ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>

                                </select>

                            </div>

                            {{-- FROM --}}
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">From</label>
                                <input type="date" name="from_date" class="form-control">
                            </div>

                            {{-- TO --}}
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">To</label>
                                <input type="date" name="to_date" class="form-control">
                            </div>

                        </div>

                        {{-- SECOND ROW --}}
                        <div class="row mt-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Search</label>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search project...">
                            </div>

                            <div class="col-md-6 d-flex justify-content-end align-items-end gap-2">

                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-funnel"></i> Generate
                                </button>

                                <a class="btn btn-danger"
                                href="{{ route('reports.projects.pdf', request()->query()) }}">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>

                                <button class="btn btn-success"
                                        type="submit"
                                        formaction="{{ route('reports.projects.excel') }}">
                                    <i class="bi bi-file-earmark-excel"></i> Excel
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            {{-- RESULT TABLE --}}
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white d-flex justify-content-between">

                    <h5 class="mb-0">Report Result</h5>

                    <span class="badge bg-primary">
                        {{ method_exists($reports, 'total') ? $reports->total() : $reports->count() }} Records
                    </span>

                </div>

                <div class="card-body">

                    @if($reports->count() > 0)

                        <div class="table-responsive">

                            <table class="table table-hover table-bordered align-middle">

                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Project</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($reports as $i => $report)

                                        <tr>

                                            <td>{{ $i + 1 }}</td>

                                            <td>{{ $report->projectname ?? '-' }}</td>

                                            <td>{{ $report->departmentname ?? '-' }}</td>

                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $report->status ?? '-' }}
                                                </span>
                                            </td>

                                            <td>{{ $report->createdby ?? '-' }}</td>

                                            <td>
                                                {{ !empty($report->createdat)
                                                    ? \Carbon\Carbon::parse($report->createdat)->format('d-m-Y')
                                                    : '-' }}
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>
                            @if(method_exists($reports, 'links'))

                                <div class="mt-4 d-flex justify-content-center">

                                    {{ $reports->links() }}

                                </div>

                            @endif

                        </div>

                    @else

                        <div class="text-center py-5">

                            <i class="bi bi-bar-chart fs-1 text-muted"></i>

                            <h5 class="mt-3">No Reports Found</h5>

                            <p class="text-muted">
                                Use filters and generate report
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection