<?php

namespace Database\Seeders;

use App\Models\SchoolSetting;
use App\Models\User;
use App\Support\TotpService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (Schema::hasTable('school_settings')) {
            SchoolSetting::set('school_name', env('SCHOOL_NAME', 'SMP Telkom Purwokerto'));
        }

        // Seed only the first Guru BK admin account if no BK user exists yet.
        $adminEmail = strtolower((string) env('ADMIN_BK_EMAIL', 'bk@smptelkompwt.sch.id'));
        $adminName = (string) env('ADMIN_BK_NAME', 'Koordinator BK SMP Telkom Purwokerto');
        $adminPassword = (string) env('ADMIN_BK_PASSWORD', 'Password-1234');

        $totp = new TotpService;

        if (! User::where('role', 'bk')->exists()) {
            User::updateOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => $adminName,
                    'password' => Hash::make($adminPassword),
                    'role' => 'bk',
                    'two_factor_secret' => $totp->generateSecret(),
                    'two_factor_recovery_codes' => $totp->generateRecoveryCodes(),
                    'two_factor_confirmed_at' => null,
                ]
            );
        }
    }
}
