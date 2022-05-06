<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Features\Whitelist;

interface WhitelistInterface
{
    /**
     * @param string $ip 
     * @return bool 
     */
    public function isWhitelisted(string $ip): bool;
}