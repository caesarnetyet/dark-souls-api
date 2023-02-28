<?php

use App\Http\Controllers\CharactersController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




Route::middleware(['auth:sanctum','role:admin'])->get('users', [UsersController::class, 'index']);

Route::get('roles', [UsersController::class, 'getRoles']);

Route::prefix('user')->group(fn () =>[
    Route::get('/', [UsersController::class, 'getUser'])
        ->middleware('auth:sanctum'),
    Route::post('/', [UsersController::class, 'register']),
    Route::post('/login', [UsersController::class, 'login']),

    Route::middleware('signed')->group(fn () =>[
        Route::get('/verify/{user}', [UsersController::class, 'verify'])
            ->name('user.verify'),

        Route::post('/verify/{user}', [UsersController::class, 'verifyPhone'])
            ->name('user.verifyCode'),

        Route::delete('/delete/{user}', [UsersController::class, 'destroy'])
            ->name('user.destroy'),

        Route::put('/update/{user}', [UsersController::class, 'update'])
            ->name('user.update')
    ]),
]);

Route::middleware(['auth:sanctum', 'active'])->group(fn ()=> [
    Route::middleware('role:employee')->get('classes',[ClassesController::class, 'index']),
    Route::prefix('class')->group(fn () => [
        Route::get('/', [ClassesController::class, 'show']),
        Route::middleware('role:employee')->group(fn ()=>[
            Route::post('/', [ClassesController::class, 'store']),
            Route::middleware('signed')->group(fn ()=> [
                Route::put('/update/{class}', [ClassesController::class, 'update'])
                    ->name('class.update'),

                Route::delete('/delete/{class}', [ClassesController::class, 'destroy'])
                    ->name('class.destroy'),
            ])
        ])
    ]),

    Route::middleware('role:user')->group(fn ()=> [
        Route::get('characters',[CharactersController::class, 'index']),
        Route::prefix('character')->group(fn () => [
            Route::post('/', [CharactersController::class, 'store']),
            Route::middleware('signed')->group(fn () => [
                Route::put('/update/{character}', [CharactersController::class, 'update'])
                    ->name('character.update'),

                Route::delete('/delete/{character}', [CharactersController::class, 'destroy'])
                    ->name('character.destroy'),
            ])
        ])

    ])
]);
