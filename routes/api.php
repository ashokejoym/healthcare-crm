<?php

use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->group(function () {
   
    // appointments Route
    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::get('/patients/{id}', [PatientController::class, 'show']);
    Route::put('/patients/{id}', [PatientController::class, 'update']);
    Route::delete('/patients/{id}', [PatientController::class, 'destroy']);

    // appointments Route
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/patient/{id}', [AppointmentController::class, 'showByPatient']);
    Route::get('/appointments/doctor/{id}', [AppointmentController::class, 'showByDoctor']);
    Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);
});
