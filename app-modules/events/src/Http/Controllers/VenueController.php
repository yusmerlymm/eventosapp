<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Events\Models\Venue;
class VenueController
{
    public function getAvailableVenues(){
        $venues = Venue::where('venue_general_status_id', 1)->get();
        return response()->json($venues);
    }
}
