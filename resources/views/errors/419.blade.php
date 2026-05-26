@extends('layouts.master')

@section('title', '419 - Session Expired')

@section('content')

<div class="container d-flex justify-content-center align-items-center"
     style="min-height: 80vh;">

    <div class="text-center">

        <h1 class="display-1 fw-bold text-warning">
            419
        </h1>

        <h3 class="mb-3">
            Session Expired
        </h3>

        <p class="text-muted mb-4">
            Your session has expired.
            Please refresh and try again.
        </p>

        <a href="{{ url()->current() }}"
           class="btn btn-warning px-4">

            Refresh Page

        </a>

    </div>

</div>

@endsection