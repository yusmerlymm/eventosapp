<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Events\Models\Venue;
use Modules\Events\Models\AvailabilityStatuses;
use Modules\Events\Models\Event;

class VenueAvailability extends Model
{
    protected $table = 'venue_availability';

    protected $fillable = [
        'venue_id',
        'availability_status_id',
        'event_id',
        'fecha_inicio',
        'fecha_fin',
    ];

    public $timestamps = true; 

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function availabilityStatus()
    {
        return $this->belongsTo(AvailabilityStatuses::class, 'availability_status_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
