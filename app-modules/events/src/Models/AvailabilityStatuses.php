<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;

class AvailabilityStatuses extends Model
{
    protected $table = 'availability_statuses';

    protected $fillable = [
        'status_name'
    ];

    public $timestamps = false; 
}
