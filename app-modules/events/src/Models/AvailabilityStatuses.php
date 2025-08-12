<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Events\Models\VenueAvailability;

class AvailabilityStatuses extends Model
{
    protected $table = 'availability_statuses';

    protected $fillable = [
        'status_name'
    ];

    public $timestamps = false; 
    public function venueAvailability()
    {
        return $this->hasMany(VenueAvailability::class, 'availability_status_id');
    }
}
