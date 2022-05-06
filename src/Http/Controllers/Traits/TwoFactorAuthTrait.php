<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Http\Controllers\Traits;

use InvalidArgumentException;
use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use SkinonikS\Laravel\TwoFactorAuth\Events\PassedEvent;
use SkinonikS\Laravel\TwoFactorAuth\Events\TokenSentEvent;
use SkinonikS\Laravel\TwoFactorAuth\Http\TwoFactorAuth;
use SkinonikS\Laravel\TwoFactorAuth\Manager;

trait TwoFactorAuthTrait
{
    /**
     * 
     */
    protected function cancel()
    {
        TwoFactorAuth::cancel();

        return $this->canceled();
    }

    /**
     * 
     */
    protected function canceled()
    {
        //
    }

    /**
     * @param string $method 
     * @return mixed 
     * @throws \Illuminate\Http\Exceptions\HttpResponseException 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException 
     */
    protected function sendToken(string $method)
    {
        $user = $this->fetchUser(
            $this->getPayload()
        );

        if (!$this->getManager()
            ->method($method)
            ->enabled($user)
        ) {
            return $this->methodDisabled($method, 'sendToken');
        }

        $token = $this->getManager()
            ->method($method)
            ->sendToken($user);
        
        $this->fireTwoFactorAuthTokenSentEvent($user, $method, $token);
            
        return $this->tokenSent($user, $method, $token);
    }

    /**
     * @param string $method 
     * @param string $action 
     */
    protected function methodDisabled(string $method, string $action)
    {
        //
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param string $method
     * @param string|null $token 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function fireTwoFactorAuthTokenSentEvent(User $user, string $method, ?string $token = null): void
    {
        if (!empty($token)) {
            event(new TokenSentEvent($user, $method, $token));
        }
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param string $method 
     * @param null|string $token 
     */
    protected function tokenSent(User $user, string $method, ?string $token = null)
    {
        //
    }

    /**
     * @param string $method 
     * @param string $token 
     * @param bool $trusted
     * @throws \Illuminate\Http\Exceptions\HttpResponseException 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Psr\Container\NotFoundExceptionInterface 
     * @throws \Psr\Container\ContainerExceptionInterface 
     * @throws \Illuminate\Validation\ValidationException 
     * @throws \InvalidArgumentException
     */
    protected function verify(string $method, string $token, bool $trusted = false)
    {
        $user = $this->fetchUser(
            $payload = $this->getPayload()
        );

        if (!$this->getManager()
            ->method($method)
            ->enabled($user)
        ) {
            return $this->methodDisabled($method, 'verify');
        }

        $verified = $this->getManager()
            ->method($method)
            ->verify($user, $token);

        if (!$verified) {
            return $this->invalidTwoFactorAuthToken($token);
        }

        $this->fireTwoFactorAuthPassedEvent($user, $trusted);

        Auth::login($user, $payload['remember'] ?? false);

        $this->fireLoginEvent($user, $payload['remember']);

        return $this->authenticated($user, $payload);
    }
    
    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param bool $remember 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function fireLoginEvent(User $user, bool $remember = false): void
    {
        event(new LoginEvent(Auth::guard(), $user, $remember));
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param array $payload 
     */
    protected function authenticated(User $user, array $payload)
    {
        TwoFactorAuth::cancel();
    }

    /**
     * @param array $payload 
     * @return \Illuminate\Foundation\Auth\User 
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException 
     */
    protected function fetchUser(array $payload): User
    {
        foreach (Config::get('auth.providers') as $provider) {
            if ($provider['driver'] === 'eloquent') {
                $model = $provider['model'];
                return $model::findOrFail($payload['user_id']);
            }
        }

        throw new InvalidArgumentException('Guard driver with `eloquent` not found.');
    }

    /**
     * @return array 
     */
    protected function getPayload(): array
    {
        return TwoFactorAuth::getPayload();
    }

    /**
     * @param string $token 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     * @throws \Psr\Container\NotFoundExceptionInterface 
     * @throws \Psr\Container\ContainerExceptionInterface 
     * @throws \Illuminate\Validation\ValidationException 
     */
    protected function invalidTwoFactorAuthToken(string $token)
    {
        throw ValidationException::withMessages([ 'token' => __('auth.two_factor_auth.token'), ]);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $user 
     * @param bool $trusted 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function fireTwoFactorAuthPassedEvent(User $user, bool $trusted = false): void
    {
        event(new PassedEvent($user, $trusted));
    }

    /**
     * @return \SkinonikS\Laravel\TwoFactorAuth\Manager 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException 
     */
    protected function getManager(): Manager
    {
        return app(Manager::class);
    }
}