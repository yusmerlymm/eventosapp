<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'ticket_type_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(EventTicketType::class, 'ticket_type_id');
    }
}
