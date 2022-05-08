<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Methods\Mail;

use Carbon\CarbonInterval;
use Illuminate\Foundation\Auth\User;
use Illuminate\Session\Store as Session;
use SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface;
use SkinonikS\Laravel\TwoFactorAuth\Methods\Traits\HasCooldownTrait;
use SkinonikS\Laravel\TwoFactorAuth\Model\HasMailMethod;
use SkinonikS\Laravel\TwoFactorAuth\Methods\TokenGenerator;

class MailMethod implements MethodInterface
{
    use HasCooldownTrait;

    /**
     * @var string
     */
    protected string $sessionKey;

    /** 
     * @param \Illuminate\Session\Store $session 
     * @param \Carbon\CarbonInterval $refreshIn 
     */
    public function __construct(
        protected Session $session,
        protected CarbonInterval $refreshIn,
    )
    {
        $this->sessionKey = $this->getSessionKey();
    }

    /**
     * {@inheritDoc}
     */
    public function enabled(User $user): bool
    {
        return $user instanceof HasMailMethod && $user->isMailMethodEnabled();
    }

    /**
     * {@inheritDoc}
     */
    public function sendToken(User $user): ?string
    {
        if (!$this->canPrepare()) {
            return null;
        }

        $this->session->put($this->sessionKey, $this->mergePayload([
            'token' => $token = TokenGenerator::generate($user),
        ]));

        return $token;
    }

    /**
     * {@inheritDoc}
     */
    public function verify(User $user, string $token): bool
    {
        $payload = $this->session->get($this->sessionKey);

        if (!$payload || !array_key_exists('token', $payload)) {
            return false;
        }

        $success = hash_equals($payload['token'], $token);

        if ($success) {
            $this->session->forget($this->sessionKey);
        }

        return $success;
    }

    /**
     * {@inheritDoc}
     */
    public function getRefreshIn(): CarbonInterval
    {
        return $this->refreshIn;
    }

    /**
     * {@inheritDoc}
     */
    protected function getPayload(): ?array
    {
        return $this->session->get($this->sessionKey);
    }

    /**
     * @return string 
     */
    protected function getSessionKey(): string
    {
        return 'two-factor-auth_'.sha1(static::class);
    }
}