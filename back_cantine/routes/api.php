<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', fn (Request $request) => $request->user())->name('user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::apiResource('students', StudentController::class);
    Route::get('/students/{student}/payments', [PaymentController::class, 'studentPayments'])
        ->name('students.payments.index');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/today', [AttendanceController::class, 'today'])->name('attendances.today');
    Route::get('/attendances/date/{date}', [AttendanceController::class, 'byDate'])
        ->where('date', '\\d{4}-\\d{2}-\\d{2}')
        ->name('attendances.by-date');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
});
