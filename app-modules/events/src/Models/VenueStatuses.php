<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;

class VenueStatuses extends Model
{
    protected $table = 'venue_general_statuses';
    
    protected $fillable = [
        'status_name',
    ];

    public $timestamps = false;

}
