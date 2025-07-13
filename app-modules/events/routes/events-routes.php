<?php
 use Illuminate\Support\Facades\Route;
 use Modules\Events\Http\Controllers\EventController;

 Route::middleware('api')->prefix('api/events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/create', [EventController::class, 'store']);
});


// Route::get('/events', [EventsController::class, 'index'])->name('events.index');
// Route::get('/events/create', [EventsController::class, 'create'])->name('events.create');
// Route::post('/events', [EventsController::class, 'store'])->name('events.store');
// Route::get('/events/{event}', [EventsController::class, 'show'])->name('events.show');
// Route::get('/events/{event}/edit', [EventsController::class, 'edit'])->name('events.edit');
// Route::put('/events/{event}', [EventsController::class, 'update'])->name('events.update');
// Route::delete('/events/{event}', [EventsController::class, 'destroy'])->name('events.destroy');
