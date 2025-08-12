<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Events\Models\Venue;

class VenueStatuses extends Model
{
    protected $table = 'venue_general_statuses';
    
    protected $fillable = [
        'status_name',
    ];

    public $timestamps = false;

    public function eventsVenues()
    {
        return $this->hasMany(Venue::class, 'venue_general_status_id');
    }

}
