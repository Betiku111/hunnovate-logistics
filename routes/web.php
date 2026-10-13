<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RiderLocationController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/track', function () {
    return 'Public tracking page — coming in STEP 6.';
})->name('track');

Route::get('/track', [OrderTrackingController::class, 'form'])->name('track');
Route::get('/track/{trackingNumber}', [OrderTrackingController::class, 'show'])->name('track.show');

// routes/web.php
Route::middleware(['auth', 'role:rider'])
    ->post('/rider/location', [RiderLocationController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('rider.location.store');

   

Route::middleware('auth')->get('/orders/{order}/tracking-data', [OrderTrackingController::class, 'show'])
    ->name('orders.tracking-data');

    Route::get('/track/{trackingNumber}/data', function (string $trackingNumber) {
    $order = \App\Models\Order::where('tracking_number', $trackingNumber)->firstOrFail();
    return app(OrderTrackingController::class)->show(request(), $order);
})->name('track.data');

// Remove these test routes if still present:
// Route::get('/layout-test', ...);
// Route::get('/component-preview', ...);
