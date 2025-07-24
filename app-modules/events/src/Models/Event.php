<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Events\Models\EventsCategory;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'capacidad_max'
    ];

    public $timestamps = true;

    public function category(){
        return $this->belongsTo(EventsCategory::class, 'id_categoria_evento');
    }
}
