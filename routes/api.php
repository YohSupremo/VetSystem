<?php

use App\Http\Controllers\Api\AppointmentQueueController;
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
});
