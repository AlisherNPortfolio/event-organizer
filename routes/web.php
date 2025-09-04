<?php

use App\Presentation\Controllers\Auth\AuthController;
use App\Presentation\Controllers\Dashboard\DashboardController;
use App\Presentation\Controllers\Event\EventController;
use App\Presentation\Controllers\Event\ParticipantController;
use App\Presentation\Controllers\Profile\ProfileController;
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
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('events', EventController::class)->except(['destroy']);

    Route::prefix('events')->group(function () {
        Route::post('{eventId}/join', [ParticipantController::class, 'join'])->name('events.join');
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('my-events', fn() => 'profile.my-events')->name('profile.my-events');
        Route::get('my-participations', fn() => 'profile.my-participations')->name('profile.my-participations');
        Route::put('update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('update', [ProfileController::class, 'uploadAvatar'])->name('profile.upload-avatar');
        Route::delete('/', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');
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
