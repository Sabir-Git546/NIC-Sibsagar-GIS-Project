@php 
    $hideNavbar = true; 
@endphp

@extends('layouts.master')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
@endsection

@section('content')

<div class="dashboard-container">

    {{-- SIDEBAR --}}
    @include('layouts.left-nav')

    {{-- MAIN CONTENT --}}
    <div class="main-content">

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    Edit User
                </h5>

            </div>


            <div class="card-body">

                <form action="{{ route('user.update', $user->id) }}"
                      method="POST">

                    @csrf
                    @method('PUT')


                    {{-- USER ID --}}
                    <div class="mb-3">

                        <label class="form-label">
                            User ID
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $user->userid }}"
                               readonly>

                    </div>


                    {{-- USER NAME --}}
                    <div class="mb-3">

                        <label class="form-label">
                            User Name
                        </label>

                        <input type="text"
                               name="username"
                               value="{{ old('username', $user->username) }}"
                               class="form-control @error('username') is-invalid @enderror"
                               required>

                        @error('username')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- EMAIL --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input type="email"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required>

                        @error('email')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- DEPARTMENT --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Department
                        </label>

                        <select name="deptid"
                                class="form-select @error('deptid') is-invalid @enderror"
                                required>

                            <option value="">
                                Select Department
                            </option>

                            @foreach($depts as $dept)

                                <option value="{{ $dept->deptid }}"
                                    {{ old('deptid', $user->deptid) == $dept->deptid ? 'selected' : '' }}>

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


                    {{-- ROLE --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Role
                        </label>

                        <select name="roleid"
                                class="form-select @error('roleid') is-invalid @enderror"
                                required>

                            <option value="">
                                Select Role
                            </option>

                            @foreach($roles as $role)

                                <option value="{{ $role->roleid }}"
                                    {{ old('roleid', $user->roleid) == $role->roleid ? 'selected' : '' }}>

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


                    {{-- BUTTONS --}}
                    <div class="d-flex justify-content-between">

                        <a href="{{ route('user.index') }}"
                           class="btn btn-secondary">

                            Back

                        </a>

                        <button type="submit"
                                class="btn btn-success"
                                onclick="this.disabled=true; this.form.submit();">

                            Update User

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection