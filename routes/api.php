<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RiderLocationController;

Route::middleware(['web', 'auth', 'role:rider'])
    ->post('/rider/location', [RiderLocationController::class, 'store'])
    ->middleware('throttle:30,1') // 30 pings per minute max
    ->name('api.rider.location');

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => config('app.name'),
        'time' => now()->toIso8601String(),
    ]);
});
