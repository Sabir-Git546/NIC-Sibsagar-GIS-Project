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

    {{-- SIDEBAR --}}
    @include('layouts.left-nav')

    {{-- MAIN CONTENT --}}
    <div class="main-content">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <h1 class="dashboard-title">
                Add User
            </h1>

        </div>


        <div class="card-body">

            <form id="addUserForm"
                  action="{{ route('user.store') }}"
                  method="POST">

                @csrf

                <div class="row mb-3">

                    <!-- USER ID -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            User Id
                        </label>

                        <input type="text"
                               name="userid"
                               value="{{ old('userid') }}"
                               class="form-control @error('userid') is-invalid @enderror"
                               required>

                        @error('userid')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- USER NAME -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            User Name
                        </label>

                        <input type="text"
                               name="username"
                               value="{{ old('username') }}"
                               class="form-control @error('username') is-invalid @enderror"
                               required>

                        @error('username')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- EMAIL -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required>

                        @error('email')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- PASSWORD -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Password
                        </label>

                        <input type="password"
                               name="userpass"
                               class="form-control @error('userpass') is-invalid @enderror"
                               required>

                        @error('userpass')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- RE ENTER PASSWORD -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Re-enter Password
                        </label>

                        <input type="password"
                               name="re_password"
                               class="form-control @error('re_password') is-invalid @enderror"
                               required>

                        @error('re_password')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- DEPARTMENT -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Department
                        </label>

                        <select name="deptid"
                                class="form-select @error('deptid') is-invalid @enderror"
                                required>

                            <option value="">
                                Select Department
                            </option>

                            @foreach(($depts ?? []) as $dept)

                                <option value="{{ $dept->deptid }}"
                                    {{ old('deptid') == $dept->deptid ? 'selected' : '' }}>

                                    {{ $dept->deptname }}

                                </option>

                            @endforeach

                        </select>

                        @error('deptid')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- ROLE -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Role
                        </label>

                        <select name="roleid"
                                class="form-select @error('roleid') is-invalid @enderror"
                                required>

                            <option value="">
                                Select Role
                            </option>

                            @foreach(($roles ?? []) as $role)

                                <option value="{{ $role->roleid }}"
                                    {{ old('roleid') == $role->roleid ? 'selected' : '' }}>

                                    {{ $role->rolename }}

                                </option>

                            @endforeach

                        </select>

                        @error('roleid')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>


                <!-- BUTTONS -->
                <div class="text-end">

                    <button type="reset"
                            class="btn btn-secondary px-4">

                        Reset

                    </button>

                    <button type="submit"
                            class="btn btn-primary px-4"
                            onclick="this.disabled=true; this.form.submit();">

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