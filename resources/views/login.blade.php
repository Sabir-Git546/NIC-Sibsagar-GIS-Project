<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RBAC Login | Sibsagar Zilla Jankaare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--css filr of this page-->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        
    </style>
</head>
<body>

<!-- Optional Top Title -->
<div class="portal-title">
    <h2>Government of Assam</h2>
    <h5>Sibsagar District Administration Portal</h5>
</div>

<div class="login-wrapper">

    <div class="card login-card">

        <div class="card-header text-center login-header py-3">
            <h4 class="mb-0">User Login</h4>
        </div>

        <div class="card-body p-4">

            <form method="POST" action="{{ route('login.submit')}}">
            @csrf

                <!-- Username -->
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" 
                           name="userid" 
                           class="form-control"
                           placeholder="Enter your userid"
                           required>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" 
                           name="userpass" 
                           class="form-control"
                           placeholder="Enter your password"
                           required>
                </div>

                <!-- Role -->
                <div class="mb-4">
                    <label class="form-label">Select Role</label>

                    <select name="roleid" class="form-select" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->roleid }}">
                                {{ $role->rolename }}
                            </option>
                        @endforeach
                    </select>

                </div>

                <!-- Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-custom">
                        Login
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>
