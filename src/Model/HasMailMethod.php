<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Model;

interface HasMailMethod
{ 
    /**
     * @return bool
     */
    public function isMailMethodEnabled(): bool;
}