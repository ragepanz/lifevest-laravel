<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AircraftController;
use Illuminate\Support\Facades\Route;

// Dashboard (homepage)
Route::get('/', DashboardController::class)->name('dashboard');

// Aircraft routes
Route::prefix('aircraft')->group(function () {
    Route::get('/{registration}', [AircraftController::class, 'show'])->name('aircraft.show');
    Route::post('/{registration}/update-seats', [AircraftController::class, 'updateSeats'])->name('aircraft.updateSeats');
    Route::delete('/{registration}/delete-seat', [AircraftController::class, 'deleteSeat'])->name('aircraft.deleteSeat');

    // PDF Report
    Route::get('/{registration}/report', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('reports.pdf');

    // Blank Form for Technicians (larger boxes for handwriting)
    Route::get('/{registration}/blank-form', [\App\Http\Controllers\ReportController::class, 'exportBlankForm'])->name('reports.blank');

    // Batch Input (Economy Only)
    Route::get('/{registration}/batch-input', [AircraftController::class, 'batchInput'])->name('aircraft.batchInput');
    Route::post('/{registration}/batch-input', [AircraftController::class, 'storeBatchInput'])->name('aircraft.storeBatchInput');
});

// Fleet Management (CRUD)
Route::resource('fleet', \App\Http\Controllers\FleetController::class);

// Airlines Management (under fleet)
Route::get('/fleet/airlines/create', [\App\Http\Controllers\FleetController::class, 'createAirline'])->name('airlines.create');
Route::post('/fleet/airlines', [\App\Http\Controllers\FleetController::class, 'storeAirline'])->name('airlines.store');
Route::get('/fleet/airlines/{id}/edit', [\App\Http\Controllers\FleetController::class, 'editAirline'])->name('airlines.edit');
Route::put('/fleet/airlines/{id}', [\App\Http\Controllers\FleetController::class, 'updateAirline'])->name('airlines.update');
Route::delete('/fleet/airlines/{id}', [\App\Http\Controllers\FleetController::class, 'destroyAirline'])->name('airlines.destroy');
