<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Events\Models\Event;

class EventStatus extends Model
{
    protected $table = "events_status";

    protected $fillable = ['status'];
    public $timestamps = true;

    public function events(){
        return $this->hasMany(Event::class, 'status');
    }

}
