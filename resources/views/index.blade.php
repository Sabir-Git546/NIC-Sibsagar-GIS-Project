@php
    $hideNavbar = true;
@endphp

@extends('layouts.master')

@section('title', 'District GIS Monitoring System')

@section('styles')

<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

/* =========================
   GLOBAL
========================= */

body {
    background: #0b1220;
    overflow-x: hidden;
    color: white;
}

/* =========================
   HERO SECTION
========================= */

.hero-section {

    position: relative;

    min-height: 92vh;

    display: flex;

    align-items: center;

    justify-content: center;

    text-align: center;

    overflow: hidden;
}

/* Background Image */

.hero-bg {

    position: absolute;

    top: 0;
    left: 0;

    width: 100%;
    height: 100%;

    object-fit: cover;

    filter: brightness(0.45);

    z-index: 1;
}

/* Overlay */

.hero-overlay {

    position: absolute;

    width: 100%;
    height: 100%;

    background:
        linear-gradient(
            to bottom,
            rgba(0,0,0,0.55),
            rgba(11,18,32,0.92)
        );

    z-index: 2;
}

/* Hero Content */

.hero-content {

    position: relative;

    z-index: 3;

    max-width: 950px;

    padding: 20px;
}

.hero-title {

    font-size: 56px;

    font-weight: 800;

    line-height: 1.2;

    margin-bottom: 25px;

    color: #ffffff;
}

.hero-subtitle {

    font-size: 20px;

    line-height: 1.8;

    color: #d0d7e2;

    margin-bottom: 40px;
}

/* Buttons */

.hero-buttons {

    display: flex;

    justify-content: center;

    gap: 20px;

    flex-wrap: wrap;
}

.btn-hero {

    padding: 14px 28px;

    border-radius: 10px;

    font-size: 16px;

    font-weight: 600;

    text-decoration: none;

    transition: 0.3s ease;

    border: none;
}

.btn-primary-custom {

    background: #00c896;

    color: white;
}

.btn-primary-custom:hover {

    background: #00a57d;

    transform: translateY(-3px);
}

.btn-outline-custom {

    border: 1px solid #00c896;

    color: #00c896;

    background: transparent;
}

.btn-outline-custom:hover {

    background: #00c896;

    color: white;

    transform: translateY(-3px);
}

/* =========================
   FEATURE SECTION
========================= */

.feature-section {

    padding: 90px 20px;
}

.section-title {

    text-align: center;

    font-size: 40px;

    font-weight: 700;

    margin-bottom: 60px;
}

/* Feature Cards */

.feature-card {

    background:
        rgba(255,255,255,0.05);

    backdrop-filter: blur(12px);

    border:
        1px solid rgba(255,255,255,0.08);

    border-radius: 18px;

    padding: 35px 25px;

    text-align: center;

    height: 100%;

    transition: 0.3s ease;
}

.feature-card:hover {

    transform: translateY(-8px);

    border-color: #00c896;

    box-shadow:
        0 10px 30px rgba(0,200,150,0.2);
}

.feature-icon {

    font-size: 45px;

    color: #00c896;

    margin-bottom: 20px;
}

.feature-card h4 {

    font-size: 22px;

    font-weight: 700;

    margin-bottom: 15px;
}

.feature-card p {

    color: #c9d4df;

    line-height: 1.7;
}

/* =========================
   SYSTEM PREVIEW
========================= */

.preview-section {

    padding: 50px 20px 100px 20px;
}

.preview-box {

    background:
        rgba(255,255,255,0.05);

    border:
        1px solid rgba(255,255,255,0.08);

    border-radius: 20px;

    overflow: hidden;

    box-shadow:
        0 10px 30px rgba(0,0,0,0.35);
}

.preview-image {

    width: 100%;

    height: 520px;

    object-fit: cover;
}

/* =========================
   FOOTER
========================= */

.footer-section {

    text-align: center;

    padding: 30px 20px;

    border-top:
        1px solid rgba(255,255,255,0.08);

    color: #b0bac5;
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width: 768px) {

    .hero-title {
        font-size: 38px;
    }

    .hero-subtitle {
        font-size: 17px;
    }

    .section-title {
        font-size: 32px;
    }

    .preview-image {
        height: 320px;
    }
}

</style>

@endsection

@section('content')

{{-- =========================
     HERO SECTION
========================= --}}
<section class="hero-section">

    {{-- Background --}}
    <img
        src="{{ asset('images/landing-page.png') }}"
        class="hero-bg"
        alt="GIS Background">

    <div class="hero-overlay"></div>

    {{-- Content --}}
    <div class="hero-content">

        <h1 class="hero-title">
            District GIS Monitoring &
            Spatial Intelligence Platform
        </h1>

        <p class="hero-subtitle">

            Integrated geospatial platform for
            monitoring, visualization and spatial
            analysis of district development projects
            using Geographic Information Systems.

        </p>

        <div class="hero-buttons">

            <a href="{{ route('login') }}"
               class="btn-hero btn-primary-custom">

                Launch GIS Portal

            </a>

            <a href="#"
               class="btn-hero btn-outline-custom">

                Explore Features

            </a>

        </div>

    </div>

</section>


{{-- =========================
     FEATURES
========================= --}}
<!--<section class="feature-section"
         id="features">

    <div class="container">

        <h2 class="section-title">
            GIS Platform Capabilities
        </h2>

        <div class="row g-4">

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>

                    <h4>Interactive GIS Mapping</h4>

                    <p>
                        Visualize district infrastructure,
                        administrative boundaries and project
                        layers interactively.
                    </p>

                </div>

            </div>

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="fas fa-draw-polygon"></i>
                    </div>

                    <h4>Spatial Analysis Tools</h4>

                    <p>
                        Perform buffer analysis,
                        overlap analysis, distance
                        measurement and feature inspection.
                    </p>

                </div>

            </div>

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>

                    <h4>Multi-Layer Management</h4>

                    <p>
                        Manage GeoJSON layers,
                        CSV visualization and
                        spatial datasets efficiently.
                    </p>

                </div>

            </div>

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>

                    <h4>GIS Reporting</h4>

                    <p>
                        Generate project reports,
                        export GIS summaries and
                        maintain audit-ready documentation.
                    </p>

                </div>

            </div>

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>

                    <h4>Approval Workflow</h4>

                    <p>
                        Controlled GIS approval
                        system for uploads,
                        edits and deletions.
                    </p>

                </div>

            </div>

            <div class="col-md-6 col-lg-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="fas fa-database"></i>
                    </div>

                    <h4>Spatial Data Management</h4>

                    <p>
                        Securely manage and
                        analyze geospatial data
                        with structured workflows.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>-->


{{-- =========================
     SYSTEM PREVIEW
========================= --}}
<!--<section class="preview-section">

    <div class="container">

        <h2 class="section-title">
            GIS Dashboard Preview
        </h2>

        <div class="preview-box">

            <img
                src="{{ asset('images/gis-dashboard-preview.jpg') }}"
                class="preview-image"
                alt="GIS Dashboard Preview">

        </div>

    </div>

</section>-->


{{-- =========================
     FOOTER
========================= --}}
<!--<footer class="footer-section">

    Powered by Laravel • PostGIS • Leaflet • GeoJSON • Turf.js

</footer>-->

@endsection