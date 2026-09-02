<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\AuthController;

Route::get('/register', [AuthController::class, 'registerView']);

Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'loginView']);

Route::post('/login', [AuthController::class, 'login']);