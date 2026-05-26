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

    <!-- SIDEBAR -->
    @include('layouts.left-nav')

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h1 class="dashboard-title">
                Add Department
            </h1>

        </div>


        <div class="card-body">

            <form action="{{ route('department.store') }}"
                  method="POST">

                @csrf

                <div class="row mb-3">

                    <!-- DEPARTMENT NAME -->
                    <div class="col-md-6">

                        <label class="form-label">
                            Department Name
                        </label>

                        <input type="text"
                               name="deptname"
                               value="{{ old('deptname') }}"
                               class="form-control @error('deptname') is-invalid @enderror"
                               placeholder="Enter department name"
                               required>

                        @error('deptname')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <!-- DEPARTMENT CODE -->
                    <div class="col-md-6">

                        <label class="form-label">
                            Department Code
                        </label>

                        <input type="text"
                               name="deptcode"
                               value="{{ old('deptcode') }}"
                               class="form-control @error('deptcode') is-invalid @enderror"
                               placeholder="Enter department code">

                        @error('deptcode')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>


                <!-- DESCRIPTION -->
                <div class="mb-3">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea name="deptdescription"
                              class="form-control @error('deptdescription') is-invalid @enderror"
                              rows="3"
                              placeholder="Enter department description">{{ old('deptdescription') }}</textarea>

                    @error('deptdescription')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <!-- ADMINISTRATIVE UNIT -->
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
                                {{ old('unitid') == $unit->unitid ? 'selected' : '' }}>

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


                <!-- BUTTONS -->
                <div class="text-end">

                    <button type="reset"
                            class="btn btn-secondary px-4">

                        Reset

                    </button>

                    <button type="submit"
                            class="btn btn-primary px-4"
                            onclick="this.disabled=true; this.form.submit();">

                        Save Department

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endauth

@endsection