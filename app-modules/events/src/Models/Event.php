<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Events\Models\EventsCategory;
use Modules\Events\Models\EventsType;
use Modules\Events\Models\EventStatus;
use Modules\Events\Models\EventsImg;
class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'capacidad_max',
        'id_categoria_evento',
        'id_tipo_evento',
        'status'
    ];

    public $timestamps = true;

    public function category(){
        return $this->belongsTo(EventsCategory::class, 'id_categoria_evento');
    }
    public function type(){
        return $this->belongsTo(EventsType::class,'id_tipo_evento');
    }
    public function eventStatus(){
        return $this->belongsTo(EventStatus::class, 'status');
    }
    public function images(){
        return $this->hasMany(EventsImg::class, 'id_evento');
    }
    public function imgPrincipal(){
        return $this->hasOne(EventsImg::class, 'id_evento')->where('es_principal', true);
    }
}
