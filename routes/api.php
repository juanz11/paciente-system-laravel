<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\RecipeFormatController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);

Route::middleware('web')->group(function () {
    Route::apiResource('patients', PatientController::class);
    Route::get('patients/{patientId}/medical-histories', [MedicalHistoryController::class, 'index']);
    Route::apiResource('medical-histories', MedicalHistoryController::class);
    Route::get('recipe-format', [RecipeFormatController::class, 'show']);
    Route::post('recipe-format', [RecipeFormatController::class, 'update']);
});
