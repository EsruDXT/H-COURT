<?php

use App\Http\Controllers\Admin\CourtController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OperatingScheduleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
Route::get('/schedule/{court}', [ScheduleController::class, 'show'])->name('schedule.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])
        ->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])
        ->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation.index');
    Route::get('/reservation/create', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
    Route::patch('/reservation/{reservation}/cancel', [ReservationController::class, 'cancel'])
        ->name('reservation.cancel');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.read-all');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/courts/{court}/operating-hours', [OperatingScheduleController::class, 'edit'])
        ->name('courts.operating-hours.edit');
    Route::put('/courts/{court}/operating-hours', [OperatingScheduleController::class, 'update'])
        ->name('courts.operating-hours.update');
    Route::post('/courts/{court}/closures', [OperatingScheduleController::class, 'storeClosure'])
        ->name('courts.closures.store');
    Route::delete('/courts/{court}/closures/{closure}', [OperatingScheduleController::class, 'destroyClosure'])
        ->name('courts.closures.destroy');

    Route::resource('courts', CourtController::class)->except('show');
    Route::patch('/courts/{court}/toggle', [CourtController::class, 'toggle'])->name('courts.toggle');

    Route::get('/reservations/{reservation}/edit', [AdminReservationController::class, 'edit'])
        ->name('reservations.edit');
    Route::put('/reservations/{reservation}', [AdminReservationController::class, 'update'])
        ->name('reservations.update');
    Route::patch('/reservations/{reservation}/approve', [AdminReservationController::class, 'approve'])
        ->name('reservations.approve');
    Route::patch('/reservations/{reservation}/reject', [AdminReservationController::class, 'reject'])
        ->name('reservations.reject');
});
