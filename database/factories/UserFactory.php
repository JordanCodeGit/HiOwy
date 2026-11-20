<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\TotpService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totp = new TotpService;

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('StrongPass#2026'),
            'role' => null,
            'two_factor_secret' => $totp->generateSecret(),
            'two_factor_recovery_codes' => $totp->generateRecoveryCodes(),
            'two_factor_confirmed_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function bk(): static
    {
        return $this->state(fn () => ['role' => 'bk']);
    }

    public function kesiswaan(): static
    {
        return $this->state(fn () => ['role' => 'kesiswaan']);
    }

    public function walas(): static
    {
        return $this->state(fn () => ['role' => 'wali_kelas']);
    }

    public function withoutTwoFactor(): static
    {
        return $this->state(fn () => ['two_factor_confirmed_at' => null]);
    }
}
