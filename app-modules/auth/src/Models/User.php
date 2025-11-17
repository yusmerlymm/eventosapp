<?php

namespace Modules\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Auth\Notifications\QueuedVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new QueuedVerifyEmail());
    }

    // Métodos helper para roles
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isComprador(): bool
    {
        return $this->role === 'comprador';
    }

    // Relación con compras
    public function purchases()
    {
        return $this->hasMany(\Modules\Events\Models\Purchase::class);
    }
}
