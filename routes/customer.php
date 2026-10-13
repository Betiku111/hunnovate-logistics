<?php

use App\Http\Controllers\Api\OrderEstimateController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::post('/estimate-fee', [OrderEstimateController::class, 'fee'])->name('estimate-fee');
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/history', [OrderController::class, 'history'])->name('history');
        Route::get('/{order}/confirmation', [OrderController::class, 'confirmation'])->name('confirmation');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    Route::get('/notifications', fn () => 'Notifications — coming in STEP 15.')
        ->name('notifications');
    Route::get('/support', fn () => 'Support — coming in STEP 20.')
        ->name('support.index');

        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
});
