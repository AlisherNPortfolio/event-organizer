<?php

use App\Presentation\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/login', function () {
        return 'login';
    })->name('login');
});
