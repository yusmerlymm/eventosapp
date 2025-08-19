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
});

// Grupo de rutas para api/admin
 Route::middleware('api')->prefix('api/admin')->group(function () {
    Route::get('/venues/available', [VenueController::class, 'getAvailableVenues']);
    Route::get('/categories/available', [EventsCategoryController::class, 'AvailableCategories']);
    Route::get('/events-type/by-category/{categoryId}', [EventsTypeController::class, 'getTypesByCategory']);
});


// Route::get('/events', [EventsController::class, 'index'])->name('events.index');
// Route::get('/events/create', [EventsController::class, 'create'])->name('events.create');
// Route::post('/events', [EventsController::class, 'store'])->name('events.store');
// Route::get('/events/{event}', [EventsController::class, 'show'])->name('events.show');
// Route::get('/events/{event}/edit', [EventsController::class, 'edit'])->name('events.edit');
// Route::put('/events/{event}', [EventsController::class, 'update'])->name('events.update');
// Route::delete('/events/{event}', [EventsController::class, 'destroy'])->name('events.destroy');
