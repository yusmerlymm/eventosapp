<?php
 use Illuminate\Support\Facades\Route;
 use Modules\Events\Http\Controllers\EventController;
 use Modules\Events\Http\Controllers\VenueController;
 use Modules\Events\Http\Controllers\EventsCategoryController;
 use Modules\Events\Http\Controllers\EventsTypeController;
 use Modules\Events\Models\EventStatus;

//  Grupo de rutas para api/events
 Route::middleware('api')->prefix('api/events')->group(function () {
    // Rutas públicas
    Route::get('/', [EventController::class, 'index']);
    
    // Rutas protegidas (solo super_admin) - URL sin conflicto
    Route::middleware(['auth:sanctum', 'role:super_admin'])->post('/store', [EventController::class, 'store']);
    
    // Rutas con {id} numérico para evitar conflicto con /create u otras rutas
    Route::get('/{id}', [EventController::class, 'show'])->whereNumber('id');
    
    Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
        Route::put('/{id}', [EventController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [EventController::class, 'destroy'])->whereNumber('id');
        Route::post('/{id}/cancel', [EventController::class, 'cancel'])->whereNumber('id');
    });
});

// Grupo de rutas para api/admin
 Route::middleware('api')->prefix('api/admin')->group(function () {
    Route::get('/venues/available', [VenueController::class, 'getAvailableVenues']);
    Route::get('/categories/available', [EventsCategoryController::class, 'AvailableCategories']);
    Route::get('/events-type/by-category/{categoryId}', [EventsTypeController::class, 'getTypesByCategory']);
    Route::get('/statuses', function () { return EventStatus::all(); });
});

// Rutas de compras (API)
use Modules\Events\Http\Controllers\PurchaseController;

Route::middleware(['api', 'auth:sanctum'])->prefix('api')->group(function () {
    Route::post('/purchases', [PurchaseController::class, 'store']);
    Route::get('/purchases', [PurchaseController::class, 'index']);
    Route::get('/purchases/{id}', [PurchaseController::class, 'show']);
    
    // Admin: ver todas las compras
    Route::middleware('role:super_admin')->get('/admin/purchases', [PurchaseController::class, 'adminIndex']);
});

// Rutas web para vistas Blade
 Route::middleware('web')->prefix('events')->group(function () {
    // Rutas públicas (todos pueden ver eventos)
    Route::get('/', function () { return view('events::index'); });
    Route::get('/{id}', function ($id) { return view('events::show', ['id' => $id]); })->whereNumber('id');
    Route::get('/{id}/purchase', function ($id) { return view('events::purchase', ['id' => $id]); })->whereNumber('id');
    
    // Rutas protegidas (solo super_admin)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/create', function () { return view('events::create-new'); });
        Route::get('/{id}/edit', function ($id) { return view('events::edit', ['id' => $id]); })->whereNumber('id');
    });
});

// Ruta para mis compras
Route::middleware('web')->get('/my-purchases', function () { return view('events::my-purchases'); });
