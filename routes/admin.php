<?php

use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OutletController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RiderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\TrackingController;
use Illuminate\Support\Facades\Route;


// Admin guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store']);
});

// Admin authenticated routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers/{customer}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle');

    Route::get('/riders', [RiderController::class, 'index'])->name('riders.index');
    Route::get('/riders/{rider}', [RiderController::class, 'show'])->name('riders.show');
    Route::post('/riders/{rider}/status', [RiderController::class, 'updateStatus'])->name('riders.status');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::post('/vehicles/{vehicle}/toggle', [VehicleController::class, 'toggleActive'])->name('vehicles.toggle');

    Route::get('/outlets', [OutletController::class, 'index'])->name('outlets.index');
    Route::post('/outlets', [OutletController::class, 'store'])->name('outlets.store');
    Route::post('/outlets/{outlet}/toggle', [OutletController::class, 'toggleActive'])->name('outlets.toggle');

    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');

    
Route::prefix('assignments')->name('assignments.')->group(function () {
    Route::get('/', [AssignmentController::class, 'index'])->name('index');
    Route::get('/{order}', [AssignmentController::class, 'show'])->name('show');
    Route::post('/{order}/assign', [AssignmentController::class, 'assign'])->name('assign');
    Route::post('/{order}/reassign', [AssignmentController::class, 'reassign'])->name('reassign');
});


Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');

    });
