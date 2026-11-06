<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\TotpService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $totp = new TotpService();
        $sampleSecret = 'JBSWY3DPEHPK3PXP';
        $recoveryCodes = json_encode([
            'SR24-8041',
            'SR24-8042',
            'SR24-8043',
            'SR24-8044',
            'BK99-1122',
            'BK99-3344',
            'BK99-5566',
            'BK99-7788',
        ]);

        $users = [
            [
                'name' => 'Sarah Jenkins, S.Psi.',
                'email' => 'konselor.sarah@nusantara.sch.id',
                'password' => Hash::make('password'),
                'role' => 'bk',
                'two_factor_secret' => $sampleSecret,
                'two_factor_recovery_codes' => $recoveryCodes,
                'two_factor_confirmed_at' => null, // Set null to trigger first-time Gmail activation flow!
            ],
            [
                'name' => 'Bambang Pratama, S.Pd.',
                'email' => 'kesiswaan@nusantara.sch.id',
                'password' => Hash::make('password'),
                'role' => 'kesiswaan',
                'two_factor_secret' => $sampleSecret,
                'two_factor_recovery_codes' => $recoveryCodes,
                'two_factor_confirmed_at' => now(), // Already confirmed
            ],
            [
                'name' => 'Ratna Dewi, S.Pd.',
                'email' => 'walas.ratna@nusantara.sch.id',
                'password' => Hash::make('password'),
                'role' => 'wali_kelas',
                'two_factor_secret' => $sampleSecret,
                'two_factor_recovery_codes' => $recoveryCodes,
                'two_factor_confirmed_at' => null, // First-time flow
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
