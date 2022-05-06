<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Features\Whitelist;

use Symfony\Component\HttpFoundation\IpUtils;

class Whitelist implements WhitelistInterface
{
    /**
     * @param \Illuminate\Http\Request $request 
     */
    public function __construct(
        protected array $allowedIps,
    )
    {
        //    
    }

    /**
     * @param string $ip 
     * @return bool
     */
    public function isWhitelisted(string $ip): bool
    {
        return IpUtils::checkIp($ip, $this->allowedIps);
    }
}