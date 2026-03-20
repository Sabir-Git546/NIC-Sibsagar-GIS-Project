@extends('layouts.master')

@section('title', 'Home')


@section('styles')
<!-- This is the css of the main content of this page -->
<link rel="stylesheet" href="{{ asset('css/index.css') }}">

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
#map {
    height: 450px;
    width: 100%;
}
</style>
@endsection

@section('content')

<div class="hero-section text-center mb-5">
    <h3>Integrated platform for monitoring and visualizing district development projects using Geographic Information Systems</h3>

</div>

<!-- Boxes -->
<div class="row justify-content-center g-4 mb-5">

    <div class="col-md-4 col-lg-3">
        <div class="counter-box" id="box3">
            <div class="box-content">
                <p>Projects Completed</p>
                <p>27</p>
                
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-3">
        <div class="counter-box" id="box2">
            <div class="box-content">
                <p>Projects Pending</p>
                <p>12</p>
            
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-3">
        <div class="counter-box" id="box3">
            <div class="box-content">
                <p>Total Projects</p>
                <p>39</p>
               
            </div>
        </div>
    </div>

</div>

<!-- Map -->
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div id="map"></div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/index.js') }}"></script>
@endsection

