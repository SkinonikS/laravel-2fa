<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Model;

interface HasMethodPreferrence
{
    /**
     * @return string|null
     */
    public function getPreferredTwoFactorAuthMethod(): ?string;
}