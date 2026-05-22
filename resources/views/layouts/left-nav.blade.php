<div class="sidebar">

    <!-- PANEL TITLE -->
    <div class="menu-title">
        @if(auth()->check() && auth()->user()->roleid == 1)
            Admin Panel
        @else
            User Panel
        @endif
    </div>

    <!-- =========================
         COMMON MENU
    ========================= -->

    <!-- Home -->
    <a class="menu-link" href="{{ route('dashboard') }}">
        Home
    </a>

    <!-- Project Management -->
    <a class="menu-link" data-bs-toggle="collapse" href="#projectMenu">
        Project / Scheme Management
    </a>

    <div class="collapse submenu" id="projectMenu">
        <a href="{{ route('projects.create') }}" class="submenu-link">
            Add Project
        </a>

        <a href="{{ route('projects.index') }}" class="submenu-link">
            View Projects
        </a>

        <a href="#" class="submenu-link">
            Update Status
        </a>
    </div>

    <!-- GIS APP -->
    <a class="menu-link" data-bs-toggle="collapse" href="#gisMenu">
        GIS App
    </a>

    <div class="collapse submenu" id="gisMenu">
        <a href="{{ route('gis.gisApp') }}" class="submenu-link">
            View Map
        </a>
    </div>

    <!-- REPORTS -->
    <a class="menu-link" data-bs-toggle="collapse" href="#reportMenu">
        Reports
    </a>

    <div class="collapse submenu" id="reportMenu">
        <a href="{{ route('reports.index') }}" class="submenu-link">
            Generate Report
        </a>

        <a href="#" class="submenu-link">
            Download Reports
        </a>
    </div>


    <!-- =========================
         ADMIN ONLY MENU
    ========================= -->

    @auth
        @if(Auth::user()->roleid == 1)

            <!-- Department Management -->
            <a class="menu-link" data-bs-toggle="collapse" href="#deptMenu">
                Department Management
            </a>

            <div class="collapse submenu" id="deptMenu">

                <a href="{{ route('department.create') }}" class="submenu-link">
                    Add Department
                </a>

                <a href="{{ route('department.index') }}" class="submenu-link">
                    View Departments
                </a>

            </div>


            <!-- User Management -->
            <a class="menu-link" data-bs-toggle="collapse" href="#userMenu">
                User Management
            </a>

            <div class="collapse submenu" id="userMenu">

                <a href="{{ route('user.create') }}" class="submenu-link">
                    Add User
                </a>

                <a href="{{ route('user.index') }}" class="submenu-link">
                    View Users
                </a>

               <!-- <a href="#" class="submenu-link">
                    Assign Roles
                </a> -->

            </div>


            <!-- District Management -->
        <!--    <a class="menu-link" data-bs-toggle="collapse" href="#administrativeMenu">
                District Management
            </a>

            <div class="collapse submenu" id="administrativeMenu">

                <a href="{{ route('dist-bound.create') }}" class="submenu-link">
                    Add Administration
                </a>

                <a href="{{ route('dist-bound.index') }}" class="submenu-link">
                    View Administration
                </a>  

            </div>  -->


            <!-- User Activity -->
            <a class="menu-link" data-bs-toggle="collapse" href="#activityMenu">
                User Activity
            </a>

            <div class="collapse submenu" id="activityMenu">

                <a href="{{ route('approvals.index') }}" class="submenu-link">
                    User Requests
                </a>

                <a href="{{ route('audit.logs') }}" class="submenu-link">
                    Activity Logs
                </a>

            </div>

        @endif
    @endauth

</div>