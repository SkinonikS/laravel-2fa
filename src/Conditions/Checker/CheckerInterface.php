<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Conditions\Checker;

use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;

interface CheckerInterface
{
    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface $token 
     * @return bool 
     */
    public function check(TokenInterface $token): bool;
}