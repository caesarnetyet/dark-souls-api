<?php

use App\Http\Controllers\ArmasController;
use App\Http\Controllers\CaracteristicasController;
use App\Http\Controllers\ClasesController;
use App\Http\Controllers\EquiposController;
use App\Http\Controllers\PersonajesController;
use App\Http\Controllers\Jefe;
use App\Http\Controllers\Juego;
use App\Http\Controllers\Mapa;
use App\Http\Controllers\UsersController;
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

Route::middleware(['auth:sanctum', 'active'])->prefix("v1")->group(function() {


    Route::prefix('armas')->group(function(){
        Route::middleware('role:guest,admin')->get('/', [ArmasController::class, 'index']);
        Route::middleware('role:admin,guest')->post('/agregar', [ArmasController::class, 'agregarArma']);
        Route::delete('/borrar', [ArmasController::class, 'borrarPorId']);
        Route::put('/actualizar', [ArmasController::class, 'actualizarArma']);
        Route::get('/personajes', [ArmasController::class, 'armaConPersonajes']);
    });
   
    Route::prefix('clases')->group(function(){
        Route::middleware('role:guest')->get('/', [ClasesController::class, 'index']);
        Route::get('/{nombre}', [ClasesController::class, 'encontrarClase']);
        Route::middleware('role:user')->post('/agregar', [ClasesController::class, 'agregarClase']);
        Route::delete('/borrar/{id}', [ClasesController::class, 'borrarPorId']);
        Route::put('/actualizar/{id}', [ClasesController::class, 'actualizarClase']);
    });

    Route::prefix('personajes')->group(function(){
        Route::middleware('role:guest,admin')->get('/', [PersonajesController::class, 'index']);
        Route::middleware('role:user')->post('/agregar', [PersonajesController::class, 'agregarPersonaje']);
        Route::delete('/borrar/{id}', [PersonajesController::class, 'borrarPorId']);
        Route::put('/actualizar/{id}', [PersonajesController::class, 'actualizarPersonaje']);
        Route::get('/armas', [PersonajesController::class, 'personajeConArmas']);
        Route::get('/carasteristica', [PersonajesController::class, 'personajeConCaracteristicas']);
        Route::prefix('caracteristicas')->group(function(){

            Route::post('/agregar', [CaracteristicasController::class, 'agregarCaracteristica']);
            Route::put('/actualizar', [CaracteristicasController::class, 'actualizarCaracteristica']);
        });    
    });
  
    Route::prefix('equipos')->group(function(){
        Route::get('/', [EquiposController::class, 'index']);
        Route::post('/agregar', [EquiposController::class, 'agregarEquipo']);
        Route::delete('/eliminar', [EquiposController::class, 'eliminarEquipo']);
    }); 

    
});

Route::prefix('usuario')->group(function(){
    Route::get('/usuariosroles', [UsersController::class, 'usuariosConRoles']);
    Route::post('/register', [UsersController::class, 'register']);
    Route::post('/login', [UsersController::class, 'login']);
   Route::middleware('auth:sanctum')->group(function(){
        Route::get('/logout', [UsersController::class, 'logout']);
        Route::get('/', [UsersController::class, 'info']);
    });

    Route::get('/verify/{user}', [UsersController::class, 'verified'])->name('verify')->middleware('signed');
    Route::post('/verifynumber', [UsersController::class, 'verifyNumber'])->name('verifynumber')->middleware('signed');
});
