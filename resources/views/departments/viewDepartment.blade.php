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

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <h1 class="dashboard-title">
                View Departments
            </h1>

        </div>


        <!-- CARD -->
        <div class="card shadow-sm">

            <div class="card-body">

                <!-- TABLE RESPONSIVE -->
                <div class="table-responsive">

                    <table class="table table-bordered table-striped align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th>ID</th>

                                <th>Department Name</th>

                                <th>Description</th>

                                <th>Administrative Unit</th>

                                <th width="150">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($departments as $dept)

                                <tr>

                                    <!-- ID -->
                                    <td>
                                        {{ $dept->deptid }}
                                    </td>


                                    <!-- NAME -->
                                    <td>
                                        {{ $dept->deptname }}
                                    </td>


                                    <!-- DESCRIPTION -->
                                    <td>
                                        {{ $dept->deptdescription ?: '-' }}
                                    </td>


                                    <!-- UNIT -->
                                    <td>
                                        {{ $dept->unit->unitname ?? '-' }}
                                    </td>


                                    <!-- ACTION -->
                                    <td class="text-center">

                                        <!-- EDIT -->
                                        <a href="{{ route('department.edit', $dept->deptid) }}"
                                           class="btn btn-sm btn-primary">

                                            Edit

                                        </a>


                                        <!-- DELETE -->
                                        <form action="{{ route('department.destroy', $dept->deptid) }}"
                                              method="POST"
                                              class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this department?')">

                                                Delete

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5"
                                        class="text-center text-muted py-4">

                                        No Departments Found

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                <!-- PAGINATION -->
                @if(method_exists($departments, 'links') && $departments->hasPages())

                    <div class="mt-4 d-flex justify-content-center">

                        {{ $departments->links() }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection