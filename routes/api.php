<?php

use App\Presentation\Controllers\Api\EventApiController;
use App\Presentation\Controllers\Api\EventPhotoApiController;
use App\Presentation\Controllers\Api\ParticipantApiController;
use App\Presentation\Controllers\Event\ParticipantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('events')->group(function () {
            Route::post('{eventId}/join', [ParticipantController::class, 'join']);
            Route::delete('{eventId}/leave', [ParticipantApiController::class, 'leave']);
            Route::get('{eventId}/participants', [ParticipantApiController::class, 'eventParticipants']);

            Route::prefix('{eventId}/photos')->group(function () {
                Route::post('/', [EventPhotoApiController::class, 'store']);
                Route::get('/', [EventPhotoApiController::class, 'index']);
            });

            Route::get('{eventId}/similar', [EventApiController::class, 'similarEvents']);
            Route::post('{eventId}/attendance', [ParticipantApiController::class, 'markAttendace']);
        });
    });
});
