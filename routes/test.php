<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Modules\Events\Models\Event;

Route::get('/test/event/{id}', function ($id) {
    $event = Event::with(['venues', 'type', 'category', 'imgPrincipal', 'images', 'eventStatus'])->findOrFail($id);
    
    // Cargar ticket types manualmente
    $ticketTypes = DB::table('event_ticket_types')->where('event_id', $id)->get();
    $event->ticket_types = $ticketTypes;
    
    return response()->json($event);
});
