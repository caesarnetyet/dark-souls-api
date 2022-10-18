<?php

use App\Http\Controllers\CaracteristicasController;
use App\Http\Controllers\ClasesController;
use App\Http\Controllers\PersonajesController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::prefix('clases')->group(function(){
        Route::get('/', [ClasesController::class, 'index']);
        Route::get('/{nombre}', [ClasesController::class, 'encontrarClase']);
        Route::post('/agregar', [ClasesController::class, 'agregarClase']);
        Route::delete('/borrar/{id}', [ClasesController::class, 'borrarPorId']);
        Route::put('/actualizar/{id}', [ClasesController::class, 'actualizarClase']);
    });

    Route::prefix('personajes')->group(function(){
        Route::get('/', [PersonajesController::class, 'index']);
        Route::get('/{nombre}', [PersonajesController::class, 'encontrarPersonaje']);
        Route::post('/agregar', [PersonajesController::class, 'agregarPersonaje']);
        Route::delete('/borrar/{id}', [PersonajesController::class, 'borrarPorId']);
        Route::put('/actualizar/{id}', [PersonajesController::class, 'actualizarPersonaje']);
        Route::prefix('caracteristicas')->group(function(){
            Route::get('/{id}', [CaracteristicasController::class, 'obtenerCaracteristica']);
            Route::post('/agregar', [CaracteristicasController::class, 'agregarCaracteristica']);
            Route::put('/actualizar', [CaracteristicasController::class, 'actualizarCaracteristica']);
        });    
    });

});