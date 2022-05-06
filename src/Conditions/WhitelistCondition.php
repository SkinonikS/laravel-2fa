<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Conditions;

use Illuminate\Http\Request;
use SkinonikS\Laravel\TwoFactorAuth\Features\Whitelist;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;

class WhitelistCondition implements ConditionInterface
{
    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Features\Whitelist $whitelist 
     */
    public function __construct(
        protected Whitelist $whitelist,
    )
    {
        //
    }

    /** 
     * {@inheritDoc}
     */
    public function shouldStart(Request $request, TokenInterface $token): bool
    {
        return !$this->whitelist->isWhitelisted($request->ip());
    }
}