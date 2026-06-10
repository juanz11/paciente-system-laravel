<?php

use App\Http\Controllers\EvolutionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::prefix('evolution')->name('evolution.')->group(function () {
    Route::get('/', [EvolutionController::class, 'index'])->name('index');
    Route::get('/{patient}', [EvolutionController::class, 'show'])->name('show');
});
