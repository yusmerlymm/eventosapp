<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventsType;

class EventsCategory extends Model
{
    protected $table = "events_category";
    protected $fillable = [
        "nombre_categoria"
    ];

    public $timestamps = true;
    public function events(){
        return $this->hasMany(Event::class, "id_categoria_evento");
    }
    public function types(){
        return $this->hasMany(EventsType::class,"id_categoria");
    }
}
