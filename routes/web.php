<?php

use App\Presentation\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerPost'])->name('register.post');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
});
Route::get('events', function () {
    return '<h1>Test page</h1>';
})->name('events.index')->middleware('auth');

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::prefix('events')->group(function () {
//         Route::get("/", function () {
//             return 'Event page';
//         })->name('events.index');
//     });
// });

Route::get('/captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha')->name('captcha');
