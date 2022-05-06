<?php

namespace SkinonikS\Laravel\TwoFactorAuth;

use Illuminate\Support\Facades\Config as LaravelConfig;

class Config
{
    /**
     * @param string $path 
     * @return bool 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public static function has(string $path): bool
    {
        $key = static::getKey();

        return LaravelConfig::has("{$key}.{$path}");
    }

    /**
     * @param null|string $path 
     * @param mixed $default 
     * @return mixed 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public static function get(?string $path = null, mixed $default = null): mixed
    {
        $key = static::getKey();

        $path = !$path
            ? $key
            : "{$key}.{$path}";

        return LaravelConfig::get($path, $default);
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'two-factor-auth';
    }
}