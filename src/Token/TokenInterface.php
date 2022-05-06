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

    // /**
    //  * @return array 
    //  */
    // public function __serialize(): array;

    // /**
    //  * @param array $data 
    //  */
    // public function __unserialize(array $data): void;
}