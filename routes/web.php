<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\CourtController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use Illuminate\Support\Facades\Route;

// -----------------------------------------------------------------
// Landing page
// -----------------------------------------------------------------
Route::get('/', [LandingController::class, 'index'])->name('landing');

// -----------------------------------------------------------------
// Jadwal lapangan
// -----------------------------------------------------------------
Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
Route::get('/schedule/{court}', [ScheduleController::class, 'show'])->name('schedule.show');

// -----------------------------------------------------------------
// Reservasi
// -----------------------------------------------------------------
Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation.index');
Route::get('/reservation/create', [ReservationController::class, 'create'])->name('reservation.create');
Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');


// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Register
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Lupa kata sandi
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');

