@php
    $hideNavbar = true;
@endphp

@extends('layouts.master')

@section('styles')

<link rel="stylesheet"
      href="{{ asset('css/adminDashboard.css') }}">

@endsection


@section('content')

<div class="dashboard-container">

    <!-- =========================
         SIDEBAR
    ========================= -->
    @include('layouts.left-nav')


    <!-- =========================
         MAIN CONTENT
    ========================= -->
    <div class="main-content">

        <!-- PAGE TITLE -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <h1 class="dashboard-title">

                @auth

                    @if(Auth::user()->roleid == 1)

                        Admin Dashboard Overview

                    @else

                        User Dashboard Overview

                    @endif

                @endauth

            </h1>

        </div>


        <!-- =========================
            STATS CARDS
        ========================= -->
        <div class="row g-4">

            <!-- DEPARTMENTS -->
            <div class="col-md-4">

                <div class="card-box text-center shadow-sm bg-secondary text-white">

                    <h4 class="text-white">
                        Departments
                    </h4>

                    <h1 id="departmentCount"
                        class="fw-bold text-white">0
                    </h1>

                </div>

            </div>


            <!-- USERS -->
            <div class="col-md-4">

                <div class="card-box text-center shadow-sm bg-success text-white">

                    <h4 class="text-white">
                        Users
                    </h4>

                    <h1 id="userCount"
                        class="fw-bold text-white"> 0
                    </h1>

                </div>

            </div>


            <!-- PROJECTS -->
            <div class="col-md-4">

                <div class="card-box text-center shadow-sm bg-info text-white">

                    <h4 class="text-white">
                        Projects
                    </h4>

                    <h1 id="projectCount"
                        class="fw-bold text-white">0
                    </h1>

                </div>

            </div>

        </div>

        <!-- =========================
            DASHBOARD ANALYTICS
        ========================= -->
        <div class="row mt-5 g-4">

            <!-- LEFT SIDE -->
            <div class="col-md-8">

                <div class="row g-4">

                    <!-- PIE CHART -->
                    <div class="col-md-6">

                        <div class="card-box shadow-sm h-100">

                            <h4 class="mb-3">
                                Department Wise Projects
                            </h4>

                            <div style="height: 300px;">

                                <canvas id="departmentChart"></canvas>

                            </div>

                        </div>

                    </div>

                    <!-- BAR GRAPH -->
                    <div class="col-md-6">

                        <div class="card-box shadow-sm h-100">

                            <h4 class="mb-3">
                                Project Status Overview
                            </h4>

                            <div style="height: 300px;">

                                <canvas id="statusChart"></canvas>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- RIGHT SIDE -->
            <div class="col-md-4">

                <!-- LIVE CLOCK -->
                <div class="card-box shadow-sm mb-4">

                    <h4 class="mb-3">
                        Live Clock
                    </h4>

                    <div id="liveClock"
                        class="fs-3 fw-bold text-primary">

                    </div>

                    <div class="text-muted">

                        {{ now()->format('l, d M Y') }}

                    </div>

                </div>


                <!-- SYSTEM INFO -->
                <div class="card-box shadow-sm">

                    <h4 class="mb-3">
                        System Info
                    </h4>

                    <p>

                        <strong>User:</strong>

                        {{ Auth::user()->username }}

                    </p>

                    <p>

                        <strong>Role:</strong>

                        @if(Auth::user()->roleid == 1)

                            Admin

                        @else

                            User

                        @endif

                    </p>

                    <p>

                        <strong>Timezone:</strong>

                        IST

                    </p>

                </div>

            </div>

        </div>

        <!-- Project List Popup -->
        <div class="modal fade"
            id="departmentProjectsModal"
            tabindex="-1"
            aria-hidden="true">

            <div class="modal-dialog modal-xl">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Department Projects List
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div id="departmentProjectsContent">

                            <div class="text-center py-4">

                                Loading...

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Recent Activity -->
        <div class="mt-5 card-box">

            <h4 class="mb-3">
                Recent Activity
            </h4>

            <ul class="list-group">

                @forelse($recentActivity as $activity)

                    <li class="list-group-item d-flex justify-content-between align-items-start">

                        <div>

                            <!-- USER -->
                            <strong>

                                @auth

                                    @if(Auth::user()->userid == $activity->userid)

                                        You

                                    @else

                                        {{ $activity->username ?? 'User' }}

                                    @endif

                                @endauth

                            </strong>


                            <!-- ACTION -->
                            @if($activity->action == 'edit')

                                <span class="text-primary ms-2">
                                    ✏️ Edit
                                </span>

                            @elseif($activity->action == 'delete')

                                <span class="text-danger ms-2">
                                    🗑 Delete
                                </span>

                            @elseif($activity->action == 'create')

                                <span class="text-success ms-2">
                                    ➕ Create
                                </span>

                            @else

                                <span class="text-secondary ms-2">
                                    ℹ️ Activity
                                </span>

                            @endif


                            <!-- MODULE -->
                            <span class="ms-1">

                                {{ ucfirst($activity->module ?? 'Module') }}

                            </span>


                            <!-- PROJECT -->
                            @if(!empty($activity->projectname))

                                :

                                <strong>
                                    {{ $activity->projectname }}
                                </strong>

                            @endif


                            <!-- STATUS -->
                            @if($activity->status == 'pending')

                                <span class="badge bg-warning text-dark ms-2">
                                    Pending
                                </span>

                            @elseif($activity->status == 'approved')

                                <span class="badge bg-success ms-2">
                                    Approved
                                </span>

                            @elseif($activity->status == 'rejected')

                                <span class="badge bg-danger ms-2">
                                    Rejected
                                </span>

                            @endif

                        </div>


                        <!-- TIME -->
                        <small class="text-muted">

                            {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}

                        </small>

                    </li>

                @empty

                    <li class="list-group-item text-center text-muted">

                        No recent activity

                    </li>

                @endforelse

            </ul>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.6.2/dist/countUp.umd.js"></script>

<script>

    // =========================================
    // DEPARTMENT PIE CHART
    // =========================================

    const departmentCtx =
        document.getElementById('departmentChart');

    new Chart(departmentCtx, {

        type: 'doughnut',

        data: {

            labels:
                @json(
                    $departmentStats->pluck('deptname')
                ),

            datasets: [{

                data:
                    @json(
                        $departmentStats->pluck('total')
                    ),

                backgroundColor: [

                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#dc3545',
                    '#6f42c1',
                    '#fd7e14',
                    '#20c997',
                    '#6610f2'
                ],

                borderWidth: 2
            }]
        },

        options: {

            responsive: true,

            plugins: {

                legend: {

                    position: 'bottom'
                }
            },

            // CLICK EVENT
            onClick: function(event, elements)
            {
                if(elements.length > 0)
                {
                    const index =
                        elements[0].index;

                    const deptname =
                        this.data.labels[index];

                    fetch(
                        `/department-projects/${encodeURIComponent(deptname)}`
                    )

                    .then(response => response.text())

                    .then(data => {

                        document.getElementById(
                            'departmentProjectsContent'
                        ).innerHTML = data;

                        const modal =
                            new bootstrap.Modal(
                                document.getElementById(
                                    'departmentProjectsModal'
                                )
                            );

                        modal.show();
                    })

                    .catch(error => {

                        console.error(error);
                    });
                }
            }
        }
    });


    // =========================================
    // PROJECT STATUS BAR GRAPH
    // =========================================

    const statusCtx =
        document.getElementById('statusChart');

    new Chart(statusCtx, {

        type: 'bar',

        data: {

            labels:
                @json(
                    $statusStats->pluck('status')
                ),

            datasets: [{

                label: 'Projects',

                data:
                    @json(
                        $statusStats->pluck('total')
                    ),

                backgroundColor: [

                    '#198754',
                    '#0d6efd',
                    '#ffc107'
                ],

                borderRadius: 8
            }]
        },

        options: {

            responsive: true,

            plugins: {

                legend: {

                    display: false
                }
            },

            scales: {

                y: {

                    beginAtZero: true
                }
            },

            // CLICK EVENT
            onClick: function(event, elements)
            {
                if(elements.length > 0)
                {
                    const index =
                        elements[0].index;

                    const status =
                        this.data.labels[index];

                    fetch(
                        `/status-projects/${encodeURIComponent(status)}`
                    )

                    .then(response => response.text())

                    .then(data => {

                        document.getElementById(
                            'departmentProjectsContent'
                        ).innerHTML = data;

                        const modal =
                            new bootstrap.Modal(
                                document.getElementById(
                                    'departmentProjectsModal'
                                )
                            );

                        modal.show();
                    })

                    .catch(error => {

                        console.error(error);
                    });
                }
            }
        }
    });


    // =========================================
    // AJAX PAGINATION INSIDE MODAL
    // =========================================

    document.addEventListener('click', function(e)
    {
        const paginationLink =
            e.target.closest('.pagination a');

        if (paginationLink)
        {
            e.preventDefault();

            fetch(paginationLink.href)

            .then(response => response.text())

            .then(data => {

                document.getElementById(
                    'departmentProjectsContent'
                ).innerHTML = data;
            })

            .catch(error => {

                console.error(error);
            });
        }
    });


    // =========================================
    // LIVE CLOCK
    // =========================================

    function updateClock()
    {
        const now =
            new Date();

        const time =
            now.toLocaleTimeString();

        document.getElementById(
            'liveClock'
        ).innerHTML = time;
    }

    setInterval(updateClock, 1000);

    updateClock();

</script>

<script>

    // =========================================
    // ANIMATED STAT CARDS
    // =========================================

    const departmentCounter =
        new countUp.CountUp(

            'departmentCount',

            {{ $totalDepartments ?? 0 }}
        );

    const userCounter =
        new countUp.CountUp(

            'userCount',

            {{ $totalUsers ?? 0 }}
        );

    const projectCounter =
        new countUp.CountUp(

            'projectCount',

            {{ $totalProjects ?? 0 }}
        );


    if (!departmentCounter.error)
    {
        departmentCounter.start();
    }

    if (!userCounter.error)
    {
        userCounter.start();
    }

    if (!projectCounter.error)
    {
        projectCounter.start();
    }

</script>
@endsection