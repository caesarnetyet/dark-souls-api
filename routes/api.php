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
 Route::middleware(['auth:sanctum','ability:armero'])->group(function(){
    Route::prefix('armas')->group(function(){
        Route::get('/', [ArmasController::class, 'index']);
        Route::post('/agregar', [ArmasController::class, 'agregarArma']);
        Route::delete('/borrar', [ArmasController::class, 'borrarPorId']);
        Route::put('/actualizar', [ArmasController::class, 'actualizarArma']);
        Route::get('/personajes', [ArmasController::class, 'armaConPersonajes']);
    });
   });
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('clases')->group(function(){
        Route::get('/', [ClasesController::class, 'index']);
        Route::get('/{nombre}', [ClasesController::class, 'encontrarClase']);
        Route::post('/agregar', [ClasesController::class, 'agregarClase']);
        Route::delete('/borrar/{id}', [ClasesController::class, 'borrarPorId']);
        Route::put('/actualizar/{id}', [ClasesController::class, 'actualizarClase']);
    });

    Route::prefix('personajes')->group(function(){
        Route::get('/', [PersonajesController::class, 'index']);
        Route::post('/agregar', [PersonajesController::class, 'agregarPersonaje']);
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

    Route::prefix('megaman')->group(function(){
        
Route::prefix('/Tipos')->group(function()
{
    Route::get('/insertar',[Tipo::class,'insertarTipo']);
    Route::get('/modificar/{id}',[Tipo::class,'modificarTipo']);
    Route::get('/consultar',[Tipo::class,'consultarTipos']);
    Route::get('/consultar/{id}',[Tipo::class,'consultarTipo']);
});
Route::prefix('/Jefes')->group(function()
{
    Route::get('/modificar/{id}',[Jefe::class,'modificarJefe']);
    Route::post('/insertar',[Jefe::class,'insertarJefe']);
    Route::get('/consultar', [jefe::class, 'consultarJefes']);
    Route::get('/consultar/{id}', [jefe::class, 'consultarJefe']);
});
Route::prefix('/Mapas')->group(function()
{
    Route::get('/modificar/{id}',[Mapa::class,'modificarMapa']);
    Route::get('/insertar',[Mapa::class,'insertarMapa']);
    Route::get('/consultar',[Mapa::class,'consultarMapas']);
});
Route::prefix('/Juegos')->group(function()
{
    Route::put('/modificar/{id}',[Juego::class,'modificarJuego']);
    Route::post('/insertar',[Juego::class,'insertarJuego']);
    Route::get('/consultar',[Juego::class,'consultarJuegos']);
    Route::get('/consultar/{id}',[Juego::class,'consultarJuego']);
});
});

});
Route::prefix('usuario')->group(function(){
    Route::post('/register', [UsersController::class, 'register']);
    Route::post('/login', [UsersController::class, 'login']);
    Route::post('/loginarmero', [UsersController::class, 'loginAsArmero']);
   Route::middleware('auth:sanctum')->group(function(){
        Route::get('/logout', [UsersController::class, 'logout']);
        Route::get('/', [UsersController::class, 'info']);
    });
});