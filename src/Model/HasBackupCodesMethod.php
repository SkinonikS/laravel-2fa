<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Model;

interface HasBackupCodesMethod
{
    /**
     * @return bool 
     */
    public function isBackupCodesMethodEnabled(): bool;

    /**
     * @param string $code 
     * @return bool 
     */
    public function hasBackupCode(string $code): bool;

    /**
     * @param string $code 
     * @return bool 
     */
    public function useBackupCode(string $code): bool;
}