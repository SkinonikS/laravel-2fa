<?php

namespace SkinonikS\Laravel\TwoFactorAuth;

class Features
{
    /**
     * 
     */
    public const TRUSTED_DEVICE = 'trusted_device';
    public const WHITELIST = 'whitelist';

    /**
     * @param string $feature 
     * @return bool 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public static function isEnabled(string $feature): bool
    {
        return Config::has("features.{$feature}");
    }

    /**
     * @return array 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public static function getWhitelistConfig(): array
    {
        return static::getFeatureConfig(static::whitelist());
    }

    /**
     * @return array 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public static function getTrustedDeviceConfig(): array
    {
        return static::getFeatureConfig(static::trustedDevice());
    }

    /** 
     * @return bool 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public static function isWhitelistEnabled(): bool
    {
        return static::isEnabled(static::whitelist());
    }

    /** 
     * @return bool 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    public static function isTrustedDeviceEnabled(): bool
    {
        return static::isEnabled(static::trustedDevice());
    }

    /**
     * @return string 
     */
    public static function trustedDevice(): string
    {
        return static::TRUSTED_DEVICE;
    }

    /**
     * @return string 
     */
    public static function whitelist(): string
    {
        return static::WHITELIST;
    }

    /**
     * @return array 
     */
    public static function getFeatures(): array
    {
        return [
            static::whitelist(),
            static::trustedDevice(),
        ];
    }

    /**
     * @return array 
     */
    public static function getEnabledFeatures(): array
    {
        return array_filter(static::getFeatures(), static function (string $feature) {
            return static::isEnabled($feature);
        });
    }

    /**
     * @param string $path 
     * @return null|array 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected static function getFeatureConfig(string $path): ?array
    {
        return Config::get("features.{$path}");
    }
}