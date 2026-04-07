<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReportRequestController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Users
    Route::prefix('users')->group(function () {
        Route::get('/general-data', [UserController::class, 'getGeneralData']);
    });

    Route::apiResource('users', UserController::class);

    // Permissions
    Route::prefix('permissions')->group(function () {
        Route::post('/general-data', [PermissionController::class, 'index']);
        Route::get('/roles/{role}', [PermissionController::class, 'getRolePermissions']);
        Route::post('/roles/{role}/assign', [PermissionController::class, 'assignPermissions']);
    });

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Equipment
    Route::apiResource('equipment', EquipmentController::class);

    // Reservations
    Route::get('/reservations/availability', [ReservationController::class, 'availability']);
    Route::post('/reservations/{id}/approve', [ReservationController::class, 'approve']);
    Route::post('/reservations/{id}/reject',  [ReservationController::class, 'reject']);
    Route::post('/reservations/{id}/cancel',  [ReservationController::class, 'cancel']);
    Route::get('/reservations/{id}/logs',     [ReservationController::class, 'logs']);
    Route::apiResource('reservations', ReservationController::class);

    // Report Requests
    Route::post('/report-requests/{id}/approve',  [ReportRequestController::class, 'approve']);
    Route::post('/report-requests/{id}/reject',   [ReportRequestController::class, 'reject']);
    Route::post('/report-requests/{id}/complete', [ReportRequestController::class, 'complete']);
    Route::apiResource('report-requests', ReportRequestController::class)->only(['index', 'store', 'show']);

    // Reports
    Route::get('/reports/stats/reservations', [ReportController::class, 'statsReservations']);
    Route::get('/reports/stats/equipment',    [ReportController::class, 'statsEquipment']);
    Route::get('/reports/{id}/download',      [ReportController::class, 'download']);
    Route::apiResource('reports', ReportController::class)->except(['update']);
});
