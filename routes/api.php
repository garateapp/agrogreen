<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CentroCostoController;
use App\Http\Controllers\Api\CuartelController;
use App\Http\Controllers\Api\TarjetaController;
use App\Http\Controllers\CosechaController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthController::class, 'token']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::get('/centros-costo', [CentroCostoController::class, 'index']);

    // Cosecha
    Route::post('/bins', [CosechaController::class, 'storeBin']);
    Route::get('/bins', [CosechaController::class, 'indexBins']);
    Route::patch('/bins/{bin}/cerrar', [CosechaController::class, 'cerrarBin']);
    Route::post('/cosechas', [CosechaController::class, 'storeCosecha']);
    Route::get('/cosechas', [CosechaController::class, 'indexCosechas']);
    Route::get('/contenedores-cosecha', [CosechaController::class, 'listarContenedores']);
    Route::get('/cuarteles', [CosechaController::class, 'listarCuarteles']);
    Route::get('/tarjetas-activas', [CosechaController::class, 'listarTarjetasActivas']);

    // Cuarteles
    Route::get('/cuarteles', [CuartelController::class, 'index']);

    // Tarjetas y Asistencia (mobile)
    Route::post('/tarjetas/scan', [TarjetaController::class, 'show']);
    Route::post('/tarjetas/asignar', [TarjetaController::class, 'assign']);
    Route::post('/asistencia/registrar', [TarjetaController::class, 'registerAttendance']);
    Route::post('/asistencia/sync', [TarjetaController::class, 'sync']);
    Route::get('/actividades', [TarjetaController::class, 'actividades']);
    Route::get('/empleados', [TarjetaController::class, 'empleados']);
});
