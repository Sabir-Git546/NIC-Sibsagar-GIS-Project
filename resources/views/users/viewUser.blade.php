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
                View Users
            </h1>

        </div>


        <!-- CARD -->
        <div class="card shadow-sm">

            <div class="card-body">

                <!-- RESPONSIVE TABLE -->
                <div class="table-responsive">

                    <table class="table table-bordered table-striped align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th>ID</th>

                                <th>User ID</th>

                                <th>User Name</th>

                                <th>Email</th>

                                <th>Department</th>

                                <th>Role</th>

                                <th width="160">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($users as $user)

                                <tr>

                                    <!-- ID -->
                                    <td>
                                        {{ $user->id }}
                                    </td>


                                    <!-- USER ID -->
                                    <td>
                                        {{ $user->userid }}
                                    </td>


                                    <!-- USER NAME -->
                                    <td>
                                        {{ $user->username }}
                                    </td>


                                    <!-- EMAIL -->
                                    <td>
                                        {{ $user->email }}
                                    </td>


                                    <!-- DEPARTMENT -->
                                    <td>
                                        {{ $user->department->deptname ?? '-' }}
                                    </td>


                                    <!-- ROLE -->
                                    <td>
                                        {{ $user->role->rolename ?? '-' }}
                                    </td>


                                    <!-- ACTION -->
                                    <td class="text-center">

                                        <!-- EDIT -->
                                        <a href="{{ route('user.edit', $user->id) }}"
                                           class="btn btn-sm btn-primary">

                                            Edit

                                        </a>


                                        <!-- DELETE -->
                                        <form action="{{ route('user.destroy', $user->id) }}"
                                              method="POST"
                                              class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this user?')">

                                                Delete

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7"
                                        class="text-center text-muted py-4">

                                        No Users Found

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                <!-- PAGINATION -->
                @if(method_exists($users, 'links') && $users->hasPages())

                    <div class="mt-4 d-flex justify-content-center">

                        {{ $users->links() }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection