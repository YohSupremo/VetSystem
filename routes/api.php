<?php

use App\Http\Controllers\Api\AppointmentQueueController;
use App\Http\Controllers\QrScanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    // Appointment Queue Management Routes
    Route::prefix('appointments')->group(function () {
        // Get filtered appointments
        Route::get('/', [AppointmentQueueController::class, 'index']);
        
        // Add new appointment
        Route::post('/', [AppointmentQueueController::class, 'store']);
        
        // Get appointment types
        Route::get('/types', [AppointmentQueueController::class, 'getAppointmentTypes']);
        
        // Get today's queue
        Route::get('/today-queue', [AppointmentQueueController::class, 'getTodaysQueue']);
        
        // Update appointment status
        Route::put('/{id}/status', [AppointmentQueueController::class, 'updateStatus']);
        
        // Get queue position
        Route::get('/{id}/queue-position', [AppointmentQueueController::class, 'getQueuePosition']);
    });

    // QR Scan Logging Routes
    Route::prefix('qr-scan')->group(function () {
        // Log a new QR scan
        Route::post('/', [QrScanController::class, 'store']);
        
        // Get all scan logs (with optional filters)
        Route::get('/', [QrScanController::class, 'index']);
        
        // Get logs for a specific cage
        Route::get('/cage/{cageId}', [QrScanController::class, 'cageLogs']);
        
        // Get logs for a specific pet
        Route::get('/pet/{petId}', [QrScanController::class, 'petLogs']);
        
        // Get logs scanned by a specific user
        Route::get('/user/{userId}', [QrScanController::class, 'userLogs']);
    });
});
