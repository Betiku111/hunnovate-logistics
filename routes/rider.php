<?php

use App\Http\Controllers\Auth\RiderRegistrationController;
use App\Http\Controllers\Rider\DashboardController;
use App\Http\Controllers\Rider\JobController;
use App\Http\Controllers\Rider\DeliveryController;
use App\Http\Controllers\Rider\ProfileController;
use Illuminate\Support\Facades\Route;

// Guest
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => redirect()->route('login'))->name('login');
    Route::get('/register', [RiderRegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RiderRegistrationController::class, 'store']);
});

// Authenticated riders
Route::middleware(['auth', 'role:rider'])->group(function () {
    // Pending verification page (accessible even before verification)
    Route::get('/pending', function () {
        return view('rider.pending');
    })->name('pending');

    // Verified-only routes — wrapped in rider.verified middleware
    Route::middleware('rider.verified')->group(function () {
        Route::get('/', DashboardController::class)->name('home');

        Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{order}', [JobController::class, 'show'])->name('jobs.show');
        Route::post('/jobs/{order}/accept', [JobController::class, 'accept'])->name('jobs.accept');
        Route::post('/jobs/{order}/reject', [JobController::class, 'reject'])->name('jobs.reject');

        Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/{order}', [DeliveryController::class, 'show'])->name('deliveries.show');
        Route::post('/deliveries/{order}/advance', [DeliveryController::class, 'advance'])->name('deliveries.advance');

        Route::get('/map', fn () => 'Rider map — coming in STEP 13.')->name('map');

        Route::get('/earnings', fn () => 'Earnings — coming in STEP 18.')->name('earnings');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/availability', [ProfileController::class, 'toggleAvailability'])->name('availability.toggle');
    });

    Route::post('/{order}/cancel', [DeliveryController::class, 'cancel'])->name('deliveries.cancel');
});