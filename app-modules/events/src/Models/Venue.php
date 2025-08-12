<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Events\Models\VenueStatuses;
use Modules\Events\Models\Event;

class Venue extends Model
{
    protected $table = 'venues';

    protected $fillable = [
        'venue_general_status_id',
        'nombre',
        'direccion',
        'capacidad_max',
    ];
    public $timestamps = true;

    public function generalStatus()
    {
        return $this->belongsTo(VenueStatuses::class, 'venue_general_status_id');
    }

    public function events(){
        return $this->hasMany(Event::class, 'venues_id');
    }
}
