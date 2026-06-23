<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\QrController;
use App\Http\Controllers\LegalPolicyController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/policies', [LegalPolicyController::class, 'apiPolicies']);
    Route::post('/policies/accept', [LegalPolicyController::class, 'apiAccept']);
    
    // Rutas de Aulas y Activos
    Route::get('/classrooms', [ClassroomController::class, 'index']);
    Route::get('/assets', [AssetController::class, 'index']);

    // Rutas de Reservas
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/calendar/me', [ReservationController::class, 'myCalendar']);

    // QR Check-in y Activos
    Route::post('/classrooms/checkin', [QrController::class, 'checkinAula']);
    Route::post('/assets/scan', [QrController::class, 'scanActivo']);
});
