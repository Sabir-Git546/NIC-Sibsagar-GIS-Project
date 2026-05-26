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
                    Edit Department
                </h5>

            </div>


            <div class="card-body">

                <form action="{{ route('department.update', $department->deptid) }}"
                      method="POST">

                    @csrf
                    @method('PUT')


                    {{-- DEPARTMENT ID --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Department ID
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $department->deptid }}"
                               readonly>

                    </div>


                    {{-- DEPARTMENT NAME --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Department Name
                        </label>

                        <input type="text"
                               name="deptname"
                               value="{{ old('deptname', $department->deptname) }}"
                               class="form-control @error('deptname') is-invalid @enderror"
                               required>

                        @error('deptname')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- DEPARTMENT DESCRIPTION --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Department Description
                        </label>

                        <textarea name="deptdescription"
                                  class="form-control @error('deptdescription') is-invalid @enderror"
                                  rows="3">{{ old('deptdescription', $department->deptdescription) }}</textarea>

                        @error('deptdescription')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ADMINISTRATIVE UNIT --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Administrative Unit
                        </label>

                        <select name="unitid"
                                class="form-select @error('unitid') is-invalid @enderror"
                                required>

                            <option value="">
                                Select Unit
                            </option>

                            @foreach($units as $unit)

                                <option value="{{ $unit->unitid }}"
                                    {{ old('unitid', $department->unitid) == $unit->unitid ? 'selected' : '' }}>

                                    {{ $unit->unitname }}
                                    ({{ $unit->unittype }})

                                </option>

                            @endforeach

                        </select>

                        @error('unitid')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- BUTTONS --}}
                    <div class="d-flex justify-content-between">

                        <a href="{{ route('department.index') }}"
                           class="btn btn-secondary">

                            Back

                        </a>


                        <button type="submit"
                                class="btn btn-success"
                                onclick="this.disabled=true; this.form.submit();">

                            Update Department

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection