    <div class="sidebar">

        <div class="menu-title">
            @if(session('roleid') == 1)
                Admin Panel
            @else
                User Panel
            @endif
        </div>

        <!--common view for user and admin-->
        <!-- Home -->
        <a class="menu-link" href="{{ route('dashboard') }}">
            Home
        </a>
        <!-- Project Management -->
        <a class="menu-link" data-bs-toggle="collapse" href="#projectMenu">
            Project / Scheme Management
        </a>
        <div class="collapse submenu" id="projectMenu">
            <a href="{{ route('projects.create') }}" class="submenu-link">Add Project</a>
            <a href="{{ route('projects.index') }}" class="submenu-link">View Projects</a>
            <a href="#" class="submenu-link">Update Status</a>
        </div>

        <!-- GIS App -->
        <a class="menu-link" data-bs-toggle="collapse" href="#gisMenu">
            GIS App
        </a>
        <div class="collapse submenu" id="gisMenu">
            <a href="{{ route('gis.gisApp') }}" class="submenu-link">View Map</a>
            <!--<a href="{{ route('gis.gisApp') }}" class="submenu-link">Layer Control</a>-->
        </div>

        <!-- Reports -->
        <a class="menu-link" data-bs-toggle="collapse" href="#reportMenu">
            Reports
        </a>
        <div class="collapse submenu" id="reportMenu">
            <a href="#" class="submenu-link">Generate Report</a>
            <a href="#" class="submenu-link">Download Reports</a>
        </div>


        <!--Admin only-->
        @if(session('roleid') == 1)

        <!-- Department Management -->
        <a class="menu-link" data-bs-toggle="collapse" href="#deptMenu">
            Department Management
        </a>
        <div class="collapse submenu" id="deptMenu">
            <a href="{{ route('department.create') }}" class="submenu-link">Add Department</a>
            <a href="{{ route('department.index') }}" class="submenu-link">View Departments</a>
        </div>

        <!-- User Management -->
        <a class="menu-link" data-bs-toggle="collapse" href="#userMenu">
            User Management
        </a>
        <div class="collapse submenu" id="userMenu">
            <a href="{{ route('user.create') }}" class="submenu-link">Add User</a>
            <a href="{{ route('user.index') }}" class="submenu-link">View User</a>
            <a href="#" class="submenu-link">Assign Roles</a>
        </div>

        <!-- Administrative Management -->
        <a class="menu-link" data-bs-toggle="collapse" href="#administrativeMenu">
            District Management
        </a>
        <div class="collapse submenu" id="administrativeMenu">
            <a href="{{ route('dist-bound.create') }}" class="submenu-link">Add Administration</a>
            <a href="{{ route('dist-bound.index') }}" class="submenu-link">View Administration</a>
        </div>
        <a class="menu-link" data-bs-toggle="collapse" href="#PermissionMenu">
            Audit Log & Permissions
        </a>
        <div class="collapse submenu" id="PermissionMenu">
            <a href="{{ route('approvals.index') }}" class="submenu-link">Permissions</a>
        </div>
        <div class="collapse submenu" id="PermissionMenu">
            <a href="{{ route('audit.logs') }}" class="submenu-link">Audit Trail</a>
        </div>
        @endif
    </div>