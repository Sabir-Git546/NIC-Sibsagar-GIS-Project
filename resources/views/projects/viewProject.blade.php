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

        <h1 class="dashboard-title mb-4">Depertment Projects View</h1>

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Project Name</th>
                            <th>Status</th>
                            <th>Department</th>
                            <th>Location (Village/Block)</th>
                            <th>Description</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th width="120">Action</th>
                            <th width="180">GIS & Document</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($projects as $prj)
                        <tr>
                            <td>{{ $prj->projectid }}</td>
                            <td>{{ $prj->projectname }}</td>
                            <td>{{ $prj->status }}</td>
                            <td>{{ $prj->department->deptname ?? '-'  }}</td>
                            <td>{{ $prj->locationUnit->unitname ?? '-' }}</td>
                            <td>{{ $prj->description }}</td>
                            <td>{{ $prj->createdby }}</td>
                            <td>{{ $prj->createdat }}</td>
                            <td class="text-center">

                                <!-- Edit Button -->
                                <a href="{{ route('projects.edit', $prj->projectid) }}"
                                   class="btn btn-sm btn-primary">
                                    edit
                                </a>

                                <!-- Delete Form -->
                                <form action="{{ route('projects.destroy', $prj->projectid) }}"
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

                            <td class="text-center">

                                <!-- gis Button -->
                                <a href="{{ route('gis.view', $prj->projectid) }}"
                                class="btn btn-sm btn-warning">
                                    + GIS
                                </a>
                                <!-- docs Button -->
                                <a href="{{--{{ route('docs.view', $prj->projectid) }}--}}"
                                   class="btn btn-sm btn-warning">
                                    + DOC
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                No Projects Found
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

























