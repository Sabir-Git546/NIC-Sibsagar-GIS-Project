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

        <h1 class="dashboard-title mb-4">View Departments</h1>

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Department Name</th>
                            <th>Description</th>
                            <th>Administrative Unit</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($departments as $dept)
                        <tr>
                            <td>{{ $dept->deptid }}</td>
                            <td>{{ $dept->deptname }}</td>
                            <td>{{ $dept->deptdescription }}</td>
                            <td>{{ $dept->unit->unitname ?? '-' }}</td>

                            <td class="text-center">

                                <a href="{{ route('department.edit', $dept->deptid) }}"
                                class="btn btn-sm btn-primary">
                                    edit
                                </a>

                                <form action="{{ route('department.destroy', $dept->deptid) }}"
                                    method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure?')">
                                        delete
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                No Departments Found
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