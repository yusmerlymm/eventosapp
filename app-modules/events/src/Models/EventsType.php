<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventsCategory;

class EventsType extends Model
{
    protected $table = 'events_type';
    protected $fillable = ['nombre_tipo_evento', 'id_categoria'];
    public $timestamps = true;
    public function category(){
        return $this->belongsTo(EventsCategory::class, 'id_categoria');
    }
    public function event(){
        return $this->hasMany(Event::class,'id_tipo_evento');
    }
}
