<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// auth
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegister')->name('show.register');
    Route::get('/login', 'showLogin')->name('show.login');
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(LocationController::class)->group(function () {
        Route::get('/locations', 'index')->name('locations.index');
        Route::get('/locations/create', 'create')->name('locations.create');
        Route::get('/locations/{location}', 'show')->name('locations.show');
        Route::post('/locations', 'store')->name('locations.store');
    });

    Route::controller(TemplateController::class)->group(function () {
        Route::get('/templates', 'index')->name('templates.index');
        Route::get('/templates/create', 'create')->name('templates.create');
        Route::get('/templates/{template}', 'show')->name('templates.show');
        Route::post('/templates', 'store')->name('templates.store');
    });

    Route::controller(InspectionController::class)->group(function () {
        Route::get('/inspections', 'index')->name('inspections.index');
        Route::get('/inspections/create', 'create')->name('inspections.create');
        Route::get('/inspections/{inspection}', 'show')->name('inspections.show');
        Route::post('/inspections', 'store')->name('inspections.store');
    });

    Route::controller(FindingController::class)->group(function () {
        Route::get('/findings', 'index')->name('findings.index');
        Route::get('/findings/{finding}', 'show')->name('findings.show');
        Route::get('/inspections/{inspection}/findings/create', 'create')->name('findings.create');
        Route::post('/inspections/{inspection}/findings', 'store')->name('findings.store');
    });

    Route::controller(ActionController::class)->group(function () {
        Route::get('/actions', 'index')->name('actions.index');
        Route::get('/actions/{action}', 'show')->name('actions.show');
        Route::get('/findings/{finding}/actions/create', 'create')->name('actions.create');
        Route::post('/findings/{finding}/actions', 'store')->name('actions.store');
        Route::post('/actions/{action}/start', 'start')->name('actions.start');
        Route::post('/actions/{action}/evidence', 'submitEvidence')->name('actions.evidence');
        Route::post('/actions/{action}/approve', 'approve')->name('actions.approve');
        Route::post('/actions/{action}/reject', 'reject')->name('actions.reject');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::patch('/users/{user}', 'update')->name('users.update');
    });
});
