<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Methods\Mail;

use Carbon\CarbonInterval;
use Closure;
use Illuminate\Foundation\Auth\User;
use Illuminate\Session\Store as Session;
use SkinonikS\Laravel\TwoFactorAuth\Methods\MethodInterface;
use SkinonikS\Laravel\TwoFactorAuth\Methods\Traits\HasCooldownTrait;
use SkinonikS\Laravel\TwoFactorAuth\Model\HasMailMethod;

class MailMethod implements MethodInterface
{
    use HasCooldownTrait;

    /** 
     * @var \Closure|null
     */
    protected static ?Closure $tokenGenerator = null;

    /**
     * @var string
     */
    protected string $key;

    /** 
     * @param \Illuminate\Session\Store $session 
     * @param \Carbon\CarbonInterval $refreshIn 
     */
    public function __construct(
        protected Session $session,
        protected CarbonInterval $refreshIn,
    )
    {
        $this->key = $this->getName();
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

        $this->session->put($this->key, $this->mergePayload([
            'token' => $token = $this->generateToken($user),
        ]));

        return $token;
    }

    /**
     * {@inheritDoc}
     */
    public function verify(User $user, string $token): bool
    {
        $payload = $this->session->pull($this->key);

        if (!$payload || !array_key_exists('token', $payload)) {
            return false;
        }

        return hash_equals($payload['token'], $token);
    }

    /**
     * @param \Closure|null $generator 
     */
    public static function setTokenGenerator(?Closure $generator): void
    {
        static::$tokenGenerator = $generator;
    }

    /** 
     * 
     */
    public static function resetTokenGenerator(): void
    {
        static::setTokenGenerator(null);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @return string 
     * @throws \Exception 
     */
    protected function generateToken(User $user): string
    {
        if (static::$tokenGenerator) {
            return (string) call_user_func_array(static::$tokenGenerator, compact('user'));
        }

        return (string) random_int(100000, 999999);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRefreshIn(): CarbonInterval
    {
        return $this->refreshIn;
    }

    /**
     * {@inheritDoc}
     */
    protected function getPayload(): ?array
    {
        return $this->session->get($this->key);
    }

    /**
     * @return string 
     */
    protected function getName(): string
    {
        return 'two-factor-auth_'.sha1(static::class);
    }
}