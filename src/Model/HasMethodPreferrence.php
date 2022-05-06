<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Model;

interface HasMethodPreferrence
{
    /**
     * @return string 
     */
    public function getPreferredTwoFactorAuthMethod(): string;
}