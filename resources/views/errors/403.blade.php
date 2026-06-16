@extends('layouts.master')

@section('title', '403 - Forbidden')

@section('content')

<div class="container d-flex justify-content-center align-items-center"
     style="min-height: 80vh;">

    <div class="text-center">

        <h1 class="display-1 fw-bold text-danger">
            403
        </h1>

        <h3 class="mb-3">
            Access Denied
        </h3>

        <p class="text-muted mb-4">
            You are not authorized to access this page.
        </p>

        <a href="{{ url()->previous() }}"
           class="btn btn-dark px-4">

            Go Back

        </a>

    </div>

</div>

@endsection