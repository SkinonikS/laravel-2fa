<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Conditions;

use Illuminate\Http\Request;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;

interface ConditionInterface
{
    /**
     * @param \Illuminate\Http\Request $request 
     * @param \SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface $token 
     * @return bool 
     */
    public function shouldStart(Request $request, TokenInterface $token): bool;
}