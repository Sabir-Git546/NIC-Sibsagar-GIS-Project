@extends('layouts.master')

@section('title', '404 - Page Not Found')

@section('content')

<div class="container d-flex justify-content-center align-items-center"
     style="min-height: 80vh;">

    <div class="text-center">

        <h1 class="display-1 fw-bold text-primary">
            404
        </h1>

        <h3 class="mb-3">
            Page Not Found
        </h3>

        <p class="text-muted mb-4">
            The page you are looking for does not exist.
        </p>

        <a href="{{ url('/dashboard') }}"
           class="btn btn-primary px-4">

            Back To Home

        </a>

    </div>

</div>

@endsection