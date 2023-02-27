<?php

use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
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



Route::get('/users', [UsersController::class, 'index']);
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

        Route::delete('/{user}', [UsersController::class, 'destroy'])
            ->name('user.destroy'),

        Route::put('/{user}', [UsersController::class, 'update'])
            ->name('user.update')
    ]),
]);
