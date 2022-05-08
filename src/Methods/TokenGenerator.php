<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Methods;

use Closure;
use Illuminate\Foundation\Auth\User;

class TokenGenerator
{
    /**
     * @var null|\Closure
     */
    protected static ?Closure $generator = null;

    /** 
     * 
     */
    public static function use(?Closure $generator): void
    {
        self::$generator = $generator;
    }

    /** 
     * 
     */
    public static function reset(): void
    {
        self::use(null);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @return string 
     * @throws \Exception 
     */
    public static function generate(User $user): string
    {
        if (static::$generator) {
            return (string) call_user_func(static::$generator, $user);
        }
        
        return (string) random_int(100000, 999999);
    }
}