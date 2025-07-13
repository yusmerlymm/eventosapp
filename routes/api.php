<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Events\Http\Controllers\EventController;
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

// rutas para el modulo de eventos
// Route::prefix('events')->group(function () {
//     Route::get('/', [EventController::class, 'index']);
// });

// Ejemplo de ruta de API que puedes usar para probar
Route::get('/mensaje-api', function () {
    return response()->json(['message' => '¡Hola desde la API de Laravel nuevamente con esta prueba!']);
});

// Si usas autenticación con Sanctum, puedes tener una ruta como esta:
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });