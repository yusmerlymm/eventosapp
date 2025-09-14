<?php

namespace Modules\Auth\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use Modules\Auth\Models\EmailVerificationToken;

class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue
{
    // Hereda todo el comportamiento de Laravel para verificación de email
    // Al implementar ShouldQueue, se encola automáticamente

    public $connection = 'redis';
    public $queue = 'default';
    public $delay = 0;

    protected function verificationUrl($notifiable){

        // generar token unico
        $token = Str::random(64);

        // guardar token en la base de datos
        EmailVerificationToken::create([
            'user_id' => $notifiable->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(60),
        ]);

        return config('app.frontend_url') . '/verify-email?token=' . $token;
    }
}