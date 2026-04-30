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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="dashboard-title">Administrative Units</h1>
            <a href="{{ route('dist-bound.create') }}" class="btn btn-primary">
                + Add New Unit
            </a>
        </div>

        {{-- TABLE --}}
        <div class="card-body">

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Unit Name</th>
                        <th>Type</th>
                        <th>Parent</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($units as $unit)
                        <tr>
                            <td>{{ $unit->unitid }}</td>
                            <td>{{ $unit->unitname }}</td>
                            <td>{{ $unit->unittype }}</td>
                            <td>
                                {{ $unit->parentUnit->unitname ?? '-' }}
                            </td>

                            <td>
                                <a href="{{ route('dist-bound.edit', $unit->unitid) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form action="{{ route('dist-bound.destroy', $unit->unitid) }}"
                                      method="POST"
                                      style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this unit?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>
</div>

@endsection