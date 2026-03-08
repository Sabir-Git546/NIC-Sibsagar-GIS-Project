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

        <h1 class="dashboard-title mb-4">View Users</h1>

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>User Email</th>
                            <th>User Address</th>
                            <th>User Phone No</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->userid }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->useremail }}</td>
                            <td>{{ $user->useraddress }}</td>
                            <td>{{ $user->userphno }}</td>
                            <td>{{ $user->department->deptname ?? '-' }}</td>
                            <td>{{ $user->role->rolename ?? '-' }}</td>
                            <td class="text-center">

                                <!-- Edit Button -->
                                <a href="{{ route('user.edit', $user->userid) }}"
                                   class="btn btn-sm btn-primary">
                                    edit
                                </a>

                                <!-- Delete Form -->
                                <form action="{{ route('user.destroy', $user->userid) }}"
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
                            <td colspan="4" class="text-center">
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