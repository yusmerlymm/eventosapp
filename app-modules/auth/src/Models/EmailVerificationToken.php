<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailVerificationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
    ];

    protected $dates = [
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
