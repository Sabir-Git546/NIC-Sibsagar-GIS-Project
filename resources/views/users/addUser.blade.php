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

    @include('layouts.left-nav')

    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="dashboard-title">Add User</h1>
        </div>

        <div class="card-body">

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ERROR MESSAGE --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form id="addUserForm" action="{{ route('user.store') }}" method="POST">
                @csrf

                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">User Id</label>
                        <input type="text" name="userid" class="form-control"
                               value="{{ old('userid') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">User Name</label>
                        <input type="text" name="username" class="form-control"
                               value="{{ old('username') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="userpass" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Re-enter Password</label>
                        <input type="password" name="re_password" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Department</label>
                        <select name="deptid" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach(($depts ?? []) as $dept)
                                <option value="{{ $dept->deptid }}">
                                    {{ $dept->deptname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select name="roleid" class="form-select" required>
                            <option value="">Select Role</option>
                            @foreach(($roles ?? []) as $role)
                                <option value="{{ $role->roleid }}">
                                    {{ $role->rolename }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="text-end">
                    <button type="reset" class="btn btn-secondary px-4">
                        Reset
                    </button>

                    <button type="submit" class="btn btn-primary px-4">
                        Save User
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endauth

@endsection

@section('scripts')
    <script src="{{ asset('js/addUserValidation.js') }}"></script>
@endsection