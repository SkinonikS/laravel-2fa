<?php
 
namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Mocks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use SkinonikS\Laravel\TwoFactorAuth\Model\HasBackupCodesMethod;
use SkinonikS\Laravel\TwoFactorAuth\Model\HasMailMethod;
use SkinonikS\Laravel\TwoFactorAuth\Model\HasMethodPreferrence;

class TestUser extends User implements HasMailMethod, HasMethodPreferrence, HasBackupCodesMethod
{
    use HasFactory;

    protected $table = 'users';

    public $incrementing = false;

    protected $keyType = 'string'; 
    
    public function isBackupCodesMethodEnabled(): bool
    {
        return true;
    }

    public function hasBackupCode(string $code): bool
    {
        return true;
    }

    public function useBackupCode(string $code): bool
    {
        return true;
    }

    public function isMailMethodEnabled(): bool
    {
        return true;
    }

    protected static function newFactory()
    {
        TestUserFactory::guessModelNamesUsing(static function () {
            return static::class;
        });

        return TestUserFactory::new();
    }

    public function getPreferredTwoFactorAuthMethod(): ?string
    {
        return $this->preferred ?? null;
    }
}