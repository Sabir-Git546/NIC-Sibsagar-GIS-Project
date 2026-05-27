<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GisController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectGisController;
use App\Http\Controllers\DistBoundController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ReportController; // ADDED


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('index'))->name('index');

Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', function (\Illuminate\Http\Request $request) {

    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('login');

})->name('logout');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/department-projects/{deptname}', [DashboardController::class, 'departmentProjects'])
        ->name('dashboard.department.projects');

    Route::get('/status-projects/{status}', [DashboardController::class, 'statusProjects'])
        ->name('dashboard.status.projects');

    /*
    |--------------------------------------------------------------------------
    | ADMIN MODULES (ROLE: 1)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:1')
        ->prefix('admin')
        ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DEPARTMENTS
        |--------------------------------------------------------------------------
        */
        Route::get('/departments', [DepartmentController::class, 'index'])
            ->name('department.index');

        Route::get('/departments/create', [DepartmentController::class, 'create'])
            ->name('department.create');

        Route::post('/departments', [DepartmentController::class, 'store'])
            ->name('department.store');

        Route::get('/departments/{deptid}/edit', [DepartmentController::class, 'edit'])
            ->name('department.edit');

        Route::put('/departments/{deptid}', [DepartmentController::class, 'update'])
            ->name('department.update');

        Route::delete('/departments/{deptid}', [DepartmentController::class, 'destroy'])
            ->name('department.destroy');


        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        Route::get('/users', [UserController::class, 'index'])
            ->name('user.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('user.create');

        Route::post('/users', [UserController::class, 'store'])
            ->name('user.store');

        Route::get('/users/{userid}/edit', [UserController::class, 'edit'])
            ->name('user.edit');

        Route::put('/users/{userid}', [UserController::class, 'update'])
            ->name('user.update');

        Route::delete('/users/{userid}', [UserController::class, 'destroy'])
            ->name('user.destroy');


        /*
        |--------------------------------------------------------------------------
        | DISTRICT BOUNDARY
        |--------------------------------------------------------------------------
        */
        Route::get('/dist-bound', [DistBoundController::class, 'index'])
            ->name('dist-bound.index');

        Route::get('/dist-bound/create', [DistBoundController::class, 'create'])
            ->name('dist-bound.create');

        Route::post('/dist-bound', [DistBoundController::class, 'store'])
            ->name('dist-bound.store');

        Route::get('/dist-bound/{unitid}/edit', [DistBoundController::class, 'edit'])
            ->name('dist-bound.edit');

        Route::put('/dist-bound/{unitid}', [DistBoundController::class, 'update'])
            ->name('dist-bound.update');

        Route::delete('/dist-bound/{unitid}', [DistBoundController::class, 'destroy'])
            ->name('dist-bound.destroy');


        /*
        |--------------------------------------------------------------------------
        | APPROVALS
        |--------------------------------------------------------------------------
        */
        Route::get('/approvals', [ApprovalController::class, 'index'])
            ->name('approvals.index');

        Route::post('/approvals/approve/{id}', [ApprovalController::class, 'approve'])
            ->name('approvals.approve');

        Route::post('/approvals/reject/{id}', [ApprovalController::class, 'reject'])
            ->name('approvals.reject');


        /*
        |--------------------------------------------------------------------------
        | AUDIT LOGS
        |--------------------------------------------------------------------------
        */
        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->name('audit.logs');
    });


    /*
    |--------------------------------------------------------------------------
    | GIS MODULE (ROLE: 1,2,3)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:1,2,3')
        ->prefix('gis')
        ->group(function () {

        Route::get('/google-map', [GisController::class, 'googleMap'])
            ->name('gis.googleMap');

        Route::get('/gis-app', [GisController::class, 'gisApp'])
            ->name('gis.gisApp');

        Route::get('/layer/{layername}', [GisController::class, 'getLayer'])
            ->name('gis.layer');

        Route::post('/save-geojson', [GisController::class, 'saveGeojson'])
            ->name('save-geojson');
    });


    /*
    |--------------------------------------------------------------------------
    | PROJECT MODULE (ROLE: 1,2,3)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:1,2,3')
        ->prefix('projects')
        ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | PROJECT CRUD
        |--------------------------------------------------------------------------
        */
        Route::get('/', [ProjectController::class, 'index'])
            ->name('projects.index');

        Route::get('/create', [ProjectController::class, 'create'])
            ->name('projects.create');

        Route::post('/', [ProjectController::class, 'store'])
            ->name('projects.store');

        Route::get('/{projectid}/edit', [ProjectController::class, 'edit'])
            ->name('projects.edit');

        Route::put('/{projectid}', [ProjectController::class, 'update'])
            ->name('projects.update');

        Route::delete('/{projectid}', [ProjectController::class, 'destroy'])
            ->name('projects.destroy');


        /*
        |--------------------------------------------------------------------------
        | PROJECT GIS
        |--------------------------------------------------------------------------
        */
        Route::get('/{projectid}/gis', [ProjectGisController::class, 'view'])
            ->name('gis.view');

        Route::get('/{projectid}/gis/upload', [ProjectGisController::class, 'uploadForm'])
            ->name('gis.upload.form');

        Route::post('/{projectid}/gis/upload', [ProjectGisController::class, 'store'])
            ->name('gis.upload.store');

        Route::delete('/{projectid}/gis/delete/{layername}', [ProjectGisController::class, 'deleteLayer'])
            ->name('gis.delete.layer');

        Route::post('/{projectid}/gis/layers', [ProjectGisController::class, 'storeLayer']) //route to save buffered files
            ->name('gis.layer.store');


        /*
        |--------------------------------------------------------------------------
        | PROJECT DOCUMENTS
        |--------------------------------------------------------------------------
        */
        Route::get('/{id}/documents/create', [ProjectDocumentController::class, 'create'])
            ->name('projects.documents.create');

        Route::post('/{id}/documents/store', [ProjectDocumentController::class, 'store'])
            ->name('projects.documents.store');
    });


    /*
    |--------------------------------------------------------------------------
    | REPORT MODULE (ROLE: 1,2,3)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:1,2,3')
        ->prefix('reports')
        ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | REPORT DASHBOARD
        |--------------------------------------------------------------------------
        */
        Route::get('/', [ReportController::class, 'index'])
            ->name('reports.index');


        /*
        |--------------------------------------------------------------------------
        | PROJECT REPORTS
        |--------------------------------------------------------------------------
        */
        Route::get('/projects', [ReportController::class, 'projectReport'])
            ->name('reports.projects');


        /*
        |--------------------------------------------------------------------------
        | PDF EXPORT
        |--------------------------------------------------------------------------
        */
        Route::get('/projects/pdf', [ReportController::class, 'exportPdf'])
            ->name('reports.projects.pdf');


        /*
        |--------------------------------------------------------------------------
        | EXCEL EXPORT
        |--------------------------------------------------------------------------
        */
        Route::get('/projects/excel', [ReportController::class, 'exportExcel'])
            ->name('reports.projects.excel');

    });

});

//Testing route
Route::get('/captcha-test', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Captcha Test</title>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body style="padding:50px;">

        <h2>Captcha Test</h2>

        <form>

            <div class="g-recaptcha"
                 data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI">
            </div>

        </form>

    </body>
    </html>
    ';
});

Route::get('/test500', function () {
    abort(500);
});

Route::get('/test403', function () {
    abort(403);
});

Route::get('/test404', function () {
    abort(404);
});

Route::get('/test419', function () {
    abort(419);
});