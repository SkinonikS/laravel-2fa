<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Conditions;

use Illuminate\Http\Request;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;
use SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice\TrustedDeviceInterface;

class TrustedDeviceCondition implements ConditionInterface
{
    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice\TrustedDeviceInterface $trustedDevice 
     */
    public function __construct(
        protected TrustedDeviceInterface $trustedDevice,
    )
    {
        //
    }

    /** 
     * {@inheritDoc}
     */
    public function shouldStart(Request $request, TokenInterface $token): bool
    {
        return !$this->trustedDevice->isCookieExists();
    }
}