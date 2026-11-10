<?php

use App\Http\Controllers\Admin\CourtController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

// ==================================================
// LANDING PAGE
// ==================================================

Route::get('/', [LandingController::class, 'index'])
    ->name('landing');

// ==================================================
// JADWAL LAPANGAN
// ==================================================

Route::get('/schedule', [ScheduleController::class, 'index'])
    ->name('schedule.index');

Route::get('/schedule/{court}', [ScheduleController::class, 'show'])
    ->name('schedule.show');

// ==================================================
// LOGIN & REGISTER
// ==================================================

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
        ->name('password.request');

});

// ==================================================
// USER YANG SUDAH LOGIN
// ==================================================

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/reservation', [ReservationController::class, 'index'])
        ->name('reservation.index');

    Route::get('/reservation/create', [ReservationController::class, 'create'])
        ->name('reservation.create');

    Route::post('/reservation', [ReservationController::class, 'store'])
        ->name('reservation.store');

});

// ==================================================
// ADMIN (hanya role pengelola)
// ==================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('courts', CourtController::class)->except('show');

    Route::patch('/courts/{court}/toggle', [CourtController::class, 'toggle'])
        ->name('courts.toggle');

    Route::get('/reservations/{reservation}/edit', [AdminReservationController::class, 'edit'])
        ->name('reservations.edit');

    Route::put('/reservations/{reservation}', [AdminReservationController::class, 'update'])
        ->name('reservations.update');

    Route::patch('/reservations/{reservation}/approve', [AdminReservationController::class, 'approve'])
        ->name('reservations.approve');

    Route::patch('/reservations/{reservation}/reject', [AdminReservationController::class, 'reject'])
        ->name('reservations.reject');

});
