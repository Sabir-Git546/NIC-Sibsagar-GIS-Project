@php 
    $hideNavbar = true; 
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
@endsection

@section('content')

<div class="dashboard-container">

    @include('layouts.left-nav')

    <div class="main-content">

        <h1 class="dashboard-title mb-4">View Users</h1>

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->userid }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>

                            <td>{{ $user->department->deptname ?? '-' }}</td>
                            <td>{{ $user->role->rolename ?? '-' }}</td>

                            <td class="text-center">

                                <a href="{{ route('user.edit', $user->id) }}"
                                   class="btn btn-sm btn-primary">
                                    edit
                                </a>

                                <form action="{{ route('user.destroy', $user->id) }}"
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
                            <td colspan="9" class="text-center">
                                No Users Found
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>

                {{-- PAGINATION --}}
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