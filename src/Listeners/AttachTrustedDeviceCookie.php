<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Listeners;

use Illuminate\Http\Request;
use SkinonikS\Laravel\TwoFactorAuth\Events\PassedEvent;
use SkinonikS\Laravel\TwoFactorAuth\Features;
use SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice\TrustedDeviceInterface;

class AttachTrustedDeviceCookie
{
    /**
     * @param \Illuminate\Http\Request $request 
     * @param \SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice\TrustedDeviceInterface $trustedDevice 
     */
    public function __construct(
        protected Request $request,
        protected TrustedDeviceInterface $trustedDevice,
    )
    {
        //
    }

    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Events\PassedEvent $event 
     */
    public function handle(PassedEvent $event): void
    {
        if (!Features::isTrustedDeviceEnabled() || !$event->trusted) {
            return;
        }

        $this->trustedDevice->attachCookie(
            $this->request->userAgent(),
        );
    }
}
