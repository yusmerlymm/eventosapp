<?php

namespace Modules\Auth\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue
{
    // Hereda todo el comportamiento de Laravel para verificación de email
    // Al implementar ShouldQueue, se encola automáticamente

    public $connection = 'redis';
    public $queue = 'default';
    public $delay = 0;
}