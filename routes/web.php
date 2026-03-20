<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;   
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GisController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectGisController;


#All Index & Login Routes

//index page route
Route::get('/', function () {
    return view('index');
})->name('index');

//show loginpage
Route::get('/login', 
    [LoginController::class, 'showLoginForm'])
->name('login');

//submit login credential and role
Route::post('/login', 
    [LoginController::class, 'login'])
->name('login.submit');

//login routes
Route::get('/dashboard', function () {

    if (!session()->has('userid')) {
        return redirect()->route('login');
    }
    return view('dashboard', [
        'hideNavbar' => true
    ]);
})->name('dashboard');

//logout route
Route::post('/logout', function () {
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('index');
})->name('logout');


#All Department Module Routes

// Show all departments
Route::get('/departments', 
    [DepartmentController::class, 'index']
)->name('department.index');

// Show add department form
Route::get('/departments/create', 
    [DepartmentController::class, 'create']
)->name('department.create');

// Store department
Route::post('/departments', 
    [DepartmentController::class, 'store']
)->name('department.store');

// Show edit form
Route::get('/departments/{deptid}/edit', 
    [DepartmentController::class, 'edit']
)->name('department.edit');

// Update department
Route::put('/departments/{deptid}', 
    [DepartmentController::class, 'update']
)->name('department.update');

// Delete department (recommended)
Route::delete('/departments/{deptid}', 
    [DepartmentController::class, 'destroy']
)->name('department.destroy');


#All User Module Routes

// show user table route
Route::get('/users', 
    [UserController::class, 'index']
)->name('user.index');

// Show add user form
Route::get('/users/create', 
    [UserController::class, 'create']
)->name('user.create');

// Store user
Route::post('/users', 
    [UserController::class, 'store']
)->name('user.store');

// Show edit user form
Route::get('/users/{userid}/edit', 
    [UserController::class, 'edit']
)->name('user.edit');

// Update existing user
Route::put('/users/{userid}', 
    [UserController::class, 'update']
)->name('user.update');

// Delete user
Route::delete('/users/{userid}', 
    [UserController::class, 'destroy']
)->name('user.destroy');


#All GIS Module Routes

// Show Google Map page
Route::get('/gis/google-map', 
    [GisController::class, 'googleMap']
)->name('gis.googleMap');

// Show Advanced GIS App
Route::get('/gis/gis-app', 
    [GisController::class, 'gisApp']
)->name('gis.gisApp');

// Convert SHP ZIP → GeoJSON and save
Route::post('/save-geojson', 
    [GisController::class, 'saveGeojson']
)->name('save-geojson');

// Fetch GIS layer from PostGIS
Route::get('/gis/layer/{layername}', 
    [GisController::class, 'getLayer']
)->name('gis.layer');


#All Projects Module Routes
// Show allprojects in table
Route::get('/projects', 
    [ProjectController::class, 'index']
)->name('projects.index');

// Show add project form
Route::get('/projects/create', 
    [ProjectController::class, 'create']
)->name('projects.create');

// Store project
Route::post('/projects', 
    [ProjectController::class, 'store']
)->name('projects.store');

// Show edit form
Route::get('/projects/{projectid}/edit', 
    [ProjectController::class, 'edit']
)->name('projects.edit');

// Update project
Route::put('/projects/{projectid}', 
    [ProjectController::class, 'update']
)->name('projects.update');

// Delete project (recommended)
Route::delete('/projects/{projectid}', 
    [ProjectController::class, 'destroy']
)->name('projects.destroy');


# Project GIS Routes

Route::get('/projects/{projectid}/gis', 
    [ProjectGisController::class,'view']
)->name('gis.view');

Route::get('/projects/{projectid}/gis/upload',
    [ProjectGisController::class,'uploadForm']
)->name('gis.upload.form');

Route::post('/projects/{projectid}/gis/upload',
    [ProjectGisController::class,'store']
)->name('gis.upload.store');

Route::delete('/projects/{projectid}/gis/delete/{layername}',
    [ProjectGisController::class, 'deleteLayer']
)->name('gis.delete.layer');


# Project Document Upload
//
Route::get('/projects/{id}/documents/create', 
    [ProjectDocumentController::class, 'create']
)->name('projects.documents.create');

//
Route::post('/projects/{id}/documents/store', 
    [ProjectDocumentController::class, 'store']
)->name('projects.documents.store');


