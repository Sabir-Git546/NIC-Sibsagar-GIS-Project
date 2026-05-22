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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>GIS Data for Project : {{ $project->projectname }}</h3>
        <div>
            <a href="{{ route('projects.index' ) }}"
           class="btn btn-warning">
            Back
        </a>

        <a href="{{ route('gis.upload.form', $project->projectid) }}"
           class="btn btn-success">
            Upload GIS File
        </a>
        </div>
        
    </div>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-striped">

                <thead class="table-dark">
                    <tr>
                        <th>GIS ID</th>
                        <th>Layer Name</th>
                        <th>Attributes</th>
                        <th>Uploaded At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($gisdata ?? [] as $gis)

                <tr>
                    <td>{{ $gis->gisdataid }}</td>
                    <td>{{ $gis->layername }}</td>
                    <td>{{ json_encode($gis->attributes) }}</td>
                    <td>{{ $gis->uploadedat }}</td>

                    <td>
                        <form action="{{ route('gis.delete.layer', [$project->projectid, $gis->layername]) }}" 
                            method="POST"
                            onsubmit="return confirm('Delete this entire GIS layer?')">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>

                @empty

                <tr>
                    <td colspan="4" class="text-center">
                        No GIS Data Available
                    </td>
                </tr>

                @endforelse

                </tbody>

            </table>

            {{-- PAGINATION --}}
            @if(method_exists($gisdata, 'links') && $gisdata->hasPages())

                <div class="mt-4 d-flex justify-content-center">

                    {{ $gisdata->links() }}

                </div>

            @endif

        </div>
    </div>

</div>

</div>

@endsection