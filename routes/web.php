<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
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


/*
|--------------------------------------------------------------------------
| INDEX + AUTH ROUTES
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', fn () => view('index'))->name('index');

// Login page
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Login submit
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    ->name('dashboard');

// Logout
Route::post('/logout', function () {
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('index');
})->name('logout');


/*
|--------------------------------------------------------------------------
| DEPARTMENT MODULE
|--------------------------------------------------------------------------
*/

Route::prefix('departments')->group(function () {

    Route::get('/', [DepartmentController::class, 'index'])->name('department.index');
    Route::get('/create', [DepartmentController::class, 'create'])->name('department.create');
    Route::post('/', [DepartmentController::class, 'store'])->name('department.store');

    Route::get('/{deptid}/edit', [DepartmentController::class, 'edit'])->name('department.edit');
    Route::put('/{deptid}', [DepartmentController::class, 'update'])->name('department.update');
    Route::delete('/{deptid}', [DepartmentController::class, 'destroy'])->name('department.destroy');
});


/*
|--------------------------------------------------------------------------
| USER MODULE
|--------------------------------------------------------------------------
*/

Route::prefix('users')->group(function () {

    Route::get('/', [UserController::class, 'index'])->name('user.index');
    Route::get('/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/', [UserController::class, 'store'])->name('user.store');

    Route::get('/{userid}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/{userid}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/{userid}', [UserController::class, 'destroy'])->name('user.destroy');
});


/*
|--------------------------------------------------------------------------
| GIS MODULE
|--------------------------------------------------------------------------
*/

Route::prefix('gis')->group(function () {

    Route::get('/google-map', [GisController::class, 'googleMap'])->name('gis.googleMap');
    Route::get('/gis-app', [GisController::class, 'gisApp'])->name('gis.gisApp');
    Route::get('/layer/{layername}', [GisController::class, 'getLayer'])->name('gis.layer');
});

Route::post('/save-geojson', [GisController::class, 'saveGeojson'])->name('save-geojson');


/*
|--------------------------------------------------------------------------
| PROJECT MODULE
|--------------------------------------------------------------------------
*/

Route::prefix('projects')->group(function () {

    Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/', [ProjectController::class, 'store'])->name('projects.store');

    Route::get('/{projectid}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/{projectid}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/{projectid}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    /*
    |--------------------------------------------------------------------------
    | PROJECT GIS
    |--------------------------------------------------------------------------
    */

    Route::get('/{projectid}/gis', [ProjectGisController::class, 'view'])->name('gis.view');

    Route::get('/{projectid}/gis/upload', [ProjectGisController::class, 'uploadForm'])->name('gis.upload.form');

    Route::post('/{projectid}/gis/upload', [ProjectGisController::class, 'store'])->name('gis.upload.store');

    Route::delete('/{projectid}/gis/delete/{layername}', [ProjectGisController::class, 'deleteLayer'])->name('gis.delete.layer');

    /*
    |--------------------------------------------------------------------------
    | PROJECT DOCUMENTS
    |--------------------------------------------------------------------------
    */

    Route::get('/{id}/documents/create', [ProjectDocumentController::class, 'create'])->name('projects.documents.create');

    Route::post('/{id}/documents/store', [ProjectDocumentController::class, 'store'])->name('projects.documents.store');
});


/*
|--------------------------------------------------------------------------
| DISTRICT BOUNDARY MODULE
|--------------------------------------------------------------------------
*/

Route::prefix('dist-bound')->group(function () {

    Route::get('/', [DistBoundController::class, 'index'])->name('dist-bound.index');
    Route::get('/create', [DistBoundController::class, 'create'])->name('dist-bound.create');
    Route::post('/', [DistBoundController::class, 'store'])->name('dist-bound.store');

    Route::get('/{unitid}/edit', [DistBoundController::class, 'edit'])->name('dist-bound.edit');
    Route::put('/{unitid}', [DistBoundController::class, 'update'])->name('dist-bound.update');
    Route::delete('/{unitid}', [DistBoundController::class, 'destroy'])->name('dist-bound.destroy');
});


/*
|--------------------------------------------------------------------------
| TEST ROUTES (REMOVE LATER IN PRODUCTION)
|--------------------------------------------------------------------------
*/

Route::get('/test-session', function () {
    dd(session()->all());
});


// ===============================
// APPROVAL ROUTES (ADMIN)
// ===============================
Route::prefix('approvals')->group(function () {

    Route::get('/', 
        [ApprovalController::class, 'index']
    )->name('approvals.index');

    Route::post('/approve/{id}', 
        [ApprovalController::class, 'approve']
    )->name('approvals.approve');

    Route::post('/reject/{id}', 
        [ApprovalController::class, 'reject']
    )->name('approvals.reject');
});


// audit trail log route
Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])
    ->name('audit.logs');