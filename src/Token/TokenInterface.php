<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Token;

interface TokenInterface
{
    /**
     * @return array
     */
    public function getEnabledMethods(): array;
    
    /**
     * @return null|string 
     */
    public function getPreferredMethod(): ?string;

    /**
     * @return string|int
     */
    public function getUserAuthIdentifier(): string|int;
}