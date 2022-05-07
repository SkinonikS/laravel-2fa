<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Conditions;

use Illuminate\Http\Request;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;
use SkinonikS\Laravel\TwoFactorAuth\Features\Whitelist\WhitelistInterface;

class WhitelistCondition implements ConditionInterface
{
    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Features\Whitelist\WhitelistInterface $whitelist 
     */
    public function __construct(
        protected WhitelistInterface $whitelist,
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