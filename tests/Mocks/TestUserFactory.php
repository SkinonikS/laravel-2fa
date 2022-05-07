<?php
 
namespace SkinonikS\Laravel\TwoFactorAuth\Tests\Mocks;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TestUserFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'nickname' => $this->faker->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}