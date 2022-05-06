<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice;

use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class TrustedDevice implements TrustedDeviceInterface
{
    /**
     * @var string
     */
    protected string $cookieName = 'device_id';

    /**
     * @var null|int
     */
    protected ?int $expiresIn = null;

    /**
     * @param \Illuminate\Cookie\CookieJar $cookie 
     * @param \Illuminate\Http\Request $request 
     */
    public function __construct(
        protected CookieJar $cookie,
        protected Request $request,
    )
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function attachCookie(string $userAgent): void
    {
        if ($this->isCookieExists()) {
            return;
        }

        $this->cookie->queue($this->createCookie($userAgent));
    }

    /**
     * {@inheritDoc}
     */
    public function isCookieExists(): bool
    {
        return $this->request->hasCookie($this->cookieName);
    }

    /**
     * @param string $userAgent 
     * @return \Symfony\Component\HttpFoundation\Cookie 
     */
    protected function createCookie(string $userAgent): SymfonyCookie
    {
        $uniqueId = sha1($userAgent);
        
        if (!$this->expiresIn) {
            return $this->cookie->forever($this->cookieName, $uniqueId);
        }

        return $this->cookie->make($this->cookieName, $uniqueId, $this->expiresIn);
    }

    /**
     * @param null|int $expiresIn 
     * @return self
     */
    public function setExpiresIn(?int $expiresIn): self
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * @param string $name 
     * @return self
     */
    public function setCookieName(string $cookieName): self
    {
        $this->cookieName = $cookieName;

        return $this;
    }

    /**
     * @param \Illuminate\Http\Request $request 
     * @return \SkinonikS\Laravel\TwoFactorAuth\Features\TrustedDevice\TrustedDevice 
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }
}