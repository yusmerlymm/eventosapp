<?php
 use Illuminate\Support\Facades\Route;
 use Modules\Events\Http\Controllers\EventController;
 use Modules\Events\Http\Controllers\VenueController;
 use Modules\Events\Http\Controllers\EventsCategoryController;
 use Modules\Events\Http\Controllers\EventsTypeController;

//  Grupo de rutas para api/events
 Route::middleware('api')->prefix('api/events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/create', [EventController::class, 'store']);
    Route::get('/{id}', [EventController::class, 'show']);
});

// Grupo de rutas para api/admin
 Route::middleware('api')->prefix('api/admin')->group(function () {
    Route::get('/venues/available', [VenueController::class, 'getAvailableVenues']);
    Route::get('/categories/available', [EventsCategoryController::class, 'AvailableCategories']);
    Route::get('/events-type/by-category/{categoryId}', [EventsTypeController::class, 'getTypesByCategory']);
});

