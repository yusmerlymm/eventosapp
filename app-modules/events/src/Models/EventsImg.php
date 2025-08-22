<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Events\Models\Event;

class EventsImg extends Model
{
    protected $table = 'events_img';

    protected $fillable = [
        'url_imagen',
        'id_evento',
        'es_principal'
    ];

    public $timestamps = false;

    public function event()
    {
        return $this->belongsTo(Event::class, 'id_evento');
    }

    public function getAbsoluteUrlAttribute()
    {
        return $this->url_imagen? url(storage::url($this->url_imagen)): null;
    }
}
