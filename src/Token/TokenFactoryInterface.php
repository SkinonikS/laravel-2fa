<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Token;

use Illuminate\Foundation\Auth\User;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;

interface TokenFactoryInterface
{
    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @return \SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface 
     */
    public function make(User $user): TokenInterface;
}