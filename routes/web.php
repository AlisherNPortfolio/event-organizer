<?php

use App\Presentation\Controllers\Auth\AuthController;
use App\Presentation\Controllers\Event\EventController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerPost'])->name('register.post');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/events', function () {
        return 'Event page';
    })->name('events.index');
    Route::get('dashboard', function () {
        return 'Dashboard page';
    })->name('dashboard');

    Route::prefix('events')->group(function () {
        Route::get('/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/', [EventController::class, 'store'])->name('events.store');
        Route::get('{event}', [EventController::class, fn () => 'Show'])->name('events.show');
        Route::get('/join', fn () => 'join')->name('events.join');
    });
});

Route::get('/events', [EventController::class, 'index'])->name('events.index');

// Email verification routes
Route::middleware('auth')->prefix('email')->group(function () {
    Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect(route('dashboard'));
    })->middleware(['signed'])->name('verification.verify');
    Route::get('/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    Route::post('/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});
// Captcha route
Route::get('/captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha')->name('captcha');
