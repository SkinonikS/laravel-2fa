<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Token;

class Token implements TokenInterface
{
    /**
     * @param string|int $userAuthIdentifier 
     * @param array $authenticators 
     * @param null|string $preferred 
     */
    public function __construct(
        protected string|int $userAuthIdentifier,
        protected array $enabledMethods,
        protected ?string $preferredMethod = null,
    )
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function getUserAuthIdentifier(): string|int
    {
        return $this->userAuthIdentifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnabledMethods(): array
    {
        return $this->enabledMethods;
    }

    /**
     * {@inheritDoc}
     */
    public function getPreferredMethod(): ?string
    {
        return $this->preferredMethod;
    }
}