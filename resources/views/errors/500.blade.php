@extends('layouts.master')

@section('title', '500 - Server Error')

@section('content')

<div class="container d-flex justify-content-center align-items-center"
     style="min-height: 80vh;">

    <div class="text-center">

        <h1 class="display-1 fw-bold text-danger">
            500
        </h1>

        <h3 class="mb-3">
            Internal Server Error
        </h3>

        <p class="text-muted mb-4">
            Something went wrong on the server.
            Please try again later.
        </p>

        <a href="{{ url('/dashboard') }}"
           class="btn btn-primary px-4">

            Return Home

        </a>

    </div>

</div>

@endsection