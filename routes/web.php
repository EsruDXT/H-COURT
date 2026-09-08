<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\Admin\CourtController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JadwalController as AdminJadwalController;
use App\Http\Controllers\Admin\ReservasiController as AdminReservasiController;
use Illuminate\Support\Facades\Route;


Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/schedule', [JadwalController::class, 'index'])->name('schedule.index');
Route::get('/schedule/{court}', [JadwalController::class, 'show'])->name('schedule.show');

Route::middleware('auth')->group(function () {
    Route::get('/reservation', [ReservasiController::class, 'index'])->name('reservation.index');
    Route::get('/reservation/create', [ReservasiController::class, 'create'])->name('reservation.create');
    Route::post('/reservation', [ReservasiController::class, 'store'])->name('reservation.store');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::post('/keluar', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


