<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Methods;

use Illuminate\Foundation\Auth\User;

interface MethodInterface
{
    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @return bool 
     */
    public function enabled(User $user): bool;

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @return string|null
     */
    public function sendToken(User $user): ?string;

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param string $token 
     * @return bool 
     */
    public function verify(User $user, string $token): bool;
}