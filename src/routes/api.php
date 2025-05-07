<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::prefix('reportes')->group(function () {
    Route::get('pregunta1', [ReportController::class, 'pregunta1']);
    Route::get('pregunta2', [ReportController::class, 'pregunta2']);
    // ... y las demás rutas para las otras preguntas
    Route::get('pregunta3', [ReportController::class, 'pregunta3']);
    Route::get('pregunta4', [ReportController::class, 'pregunta4']);
    Route::get('pregunta5', [ReportController::class, 'pregunta5']);
    Route::get('pregunta6', [ReportController::class, 'pregunta6']);
    Route::get('pregunta7', [ReportController::class, 'pregunta7']);
    Route::get('pregunta8', [ReportController::class, 'pregunta8']);
    Route::get('pregunta9', [ReportController::class, 'pregunta9']);
    Route::get('pregunta10', [ReportController::class, 'pregunta10']);
    Route::get('pregunta11', [ReportController::class, 'pregunta11']);
    Route::get('examen', [ReportController::class, 'examen']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
