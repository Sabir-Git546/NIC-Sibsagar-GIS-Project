@php
    $hideNavbar = true;
@endphp

@extends('layouts.master')

@section('title', 'Reports')

@section('content')

<div class="container-fluid py-3">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Project Reports
            </h3>

            <p class="text-muted mb-0">
                Generate and export project related reports
            </p>

        </div>

    </div>


    {{-- FILTER CARD --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white">

            <h5 class="mb-0">
                Report Filters
            </h5>

        </div>

        <div class="card-body">

            <form method="GET" action="{{ route('reports.projects') }}">

                <div class="row g-3">

                    {{-- REPORT TYPE --}}
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Report Type
                        </label>

                        <select
                            name="report_type"
                            class="form-select"
                        >

                            <option value="master"
                                {{ request('report_type') == 'master' ? 'selected' : '' }}
                            >
                                Master Project Report
                            </option>

                            <option value="status"
                                {{ request('report_type') == 'status' ? 'selected' : '' }}
                            >
                                Project Status Summary
                            </option>

                            <option value="department"
                                {{ request('report_type') == 'department' ? 'selected' : '' }}
                            >
                                Department Wise Report
                            </option>

                            <option value="gis"
                                {{ request('report_type') == 'gis' ? 'selected' : '' }}
                            >
                                GIS Layer Summary
                            </option>

                            <option value="geometry"
                                {{ request('report_type') == 'geometry' ? 'selected' : '' }}
                            >
                                Geometry Type Summary
                            </option>

                        </select>

                    </div>


                    {{-- DEPARTMENT --}}
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Department
                        </label>

                        <select
                            name="deptid"
                            class="form-select"
                        >

                            <option value="">
                                All Departments
                            </option>

                            @foreach($departments as $department)

                                <option
                                    value="{{ $department->deptid }}"
                                    {{ request('deptid') == $department->deptid ? 'selected' : '' }}
                                >

                                    {{ $department->deptname }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- STATUS --}}
                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option value="Pending"
                                {{ request('status') == 'Pending' ? 'selected' : '' }}
                            >
                                Pending
                            </option>

                            <option value="Active"
                                {{ request('status') == 'Active' ? 'selected' : '' }}
                            >
                                Active
                            </option>

                            <option value="Completed"
                                {{ request('status') == 'Completed' ? 'selected' : '' }}
                            >
                                Completed
                            </option>

                            <option value="Rejected"
                                {{ request('status') == 'Rejected' ? 'selected' : '' }}
                            >
                                Rejected
                            </option>

                        </select>

                    </div>


                    {{-- FROM DATE --}}
                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            From Date
                        </label>

                        <input
                            type="date"
                            name="from_date"
                            class="form-control"
                            value="{{ request('from_date') }}"
                        >

                    </div>


                    {{-- TO DATE --}}
                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            To Date
                        </label>

                        <input
                            type="date"
                            name="to_date"
                            class="form-control"
                            value="{{ request('to_date') }}"
                        >

                    </div>

                </div>


                {{-- SECOND ROW --}}
                <div class="row mt-3">

                    {{-- SEARCH --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Search Project
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Enter project name..."
                            value="{{ request('search') }}"
                        >

                    </div>


                    {{-- ACTION BUTTONS --}}
                    <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">

                        {{-- GENERATE --}}
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-file-earmark-text"></i>

                            Generate Report

                        </button>


                        {{-- PDF --}}
                        <button
                            type="submit"
                            formaction="{{ route('reports.projects.pdf') }}"
                            class="btn btn-danger"
                        >

                            <i class="bi bi-file-earmark-pdf"></i>

                            Export PDF

                        </button>


                        {{-- EXCEL --}}
                        <button
                            type="submit"
                            formaction="{{ route('reports.projects.excel') }}"
                            class="btn btn-success"
                        >

                            <i class="bi bi-file-earmark-excel"></i>

                            Export Excel

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- REPORT RESULT --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Report Result
            </h5>

            <span class="badge bg-primary">

                {{ isset($reports) ? count($reports) : 0 }}

                Records

            </span>

        </div>


        <div class="card-body">

            @if(isset($reports) && count($reports) > 0)

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="60">#</th>

                                <th>Project Name</th>

                                <th>Department</th>

                                <th>Status</th>

                                <th>Created By</th>

                                <th>Created At</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($reports as $index => $report)

                                <tr>

                                    <td>
                                        {{ $index + 1 }}
                                    </td>


                                    {{-- PROJECT NAME --}}
                                    <td>
                                        {{ $report->projectname ?? '-' }}
                                    </td>


                                    {{-- DEPARTMENT --}}
                                    <td>
                                        {{ $report->departmentname ?? '-' }}
                                    </td>


                                    {{-- STATUS --}}
                                    <td>

                                        @php

                                            $badge = 'secondary';

                                            if (($report->status ?? '') == 'Active') {
                                                $badge = 'success';
                                            }

                                            elseif (($report->status ?? '') == 'Pending') {
                                                $badge = 'warning';
                                            }

                                            elseif (($report->status ?? '') == 'Completed') {
                                                $badge = 'primary';
                                            }

                                            elseif (($report->status ?? '') == 'Rejected') {
                                                $badge = 'danger';
                                            }

                                        @endphp

                                        <span class="badge bg-{{ $badge }}">

                                            {{ $report->status ?? '-' }}

                                        </span>

                                    </td>


                                    {{-- CREATED BY --}}
                                    <td>
                                        {{ $report->createdby ?? '-' }}
                                    </td>


                                    {{-- CREATED DATE --}}
                                    <td>

                                        @if(!empty($report->createdat))

                                            {{ \Carbon\Carbon::parse($report->createdat)->format('d-m-Y') }}

                                        @else

                                            -

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="text-center py-5">

                    <i class="bi bi-bar-chart fs-1 text-muted"></i>

                    <h5 class="mt-3">
                        No Report Generated
                    </h5>

                    <p class="text-muted mb-0">
                        Select filters and click Generate Report
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection