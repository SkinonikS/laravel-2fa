<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice;

interface TrustedDeviceInterface
{
    /**
     * @param string $userAgent 
     */
    public function attachCookie(string $userAgent): void;

    /**
     * @return bool 
     */
    public function isCookieExists(): bool;
}
