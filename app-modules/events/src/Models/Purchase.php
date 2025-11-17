<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'codigo_compra',
        'total',
        'estado'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // Generar código único de compra
    public static function generateCode()
    {
        do {
            $code = 'COMP-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (self::where('codigo_compra', $code)->exists());
        
        return $code;
    }
}
