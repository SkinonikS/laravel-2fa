<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Conditions;

use Illuminate\Http\Request;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;

class AnyMethodCondition implements ConditionInterface
{
    /** 
     * {@inheritDoc}
     */
    public function shouldStart(Request $request, TokenInterface $token): bool
    {
        return count($token->getEnabledMethods()) > 0;
    }
}