<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Events;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PassedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param bool $trusted 
     */
    public function __construct(
        public User $user,
        public string $method,
        public bool $trusted = false,
    )
    {
        //
    }
}
