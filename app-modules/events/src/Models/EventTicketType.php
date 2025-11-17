<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicketType extends Model
{
    protected $table = 'event_ticket_types';

    protected $fillable = [
        'event_id',
        'nombre',
        'precio',
    ];

    public $timestamps = true;

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
