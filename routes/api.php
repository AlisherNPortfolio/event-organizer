<?php

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
        });
    });
});
