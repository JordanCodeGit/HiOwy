<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\TotpService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateBkAdminCommand extends Command
{
    protected $signature = 'hiowy:create-admin
                            {--email= : Email admin guru BK}
                            {--name= : Nama lengkap admin guru BK}
                            {--password= : Kata sandi (minimal 10 karakter)}';

    protected $description = 'Buat atau inisialisasi akun Guru BK pertama (Admin Sekolah) secara aman.';

    public function handle(TotpService $totp): int
    {
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Email Guru BK Admin', 'bk@smptelkompwt.sch.id'))));
        $name = trim((string) ($this->option('name') ?: $this->ask('Nama Lengkap Guru BK', 'Koordinator BK SMP Telkom Purwokerto')));
        $password = (string) ($this->option('password') ?: $this->secret('Kata Sandi (min 10 karakter, huruf besar/kecil, angka, simbol)'));

        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password,
        ], [
            'email' => ['required', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', Password::min(10)->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'bk',
                'two_factor_secret' => $totp->generateSecret(),
                'two_factor_recovery_codes' => $totp->generateRecoveryCodes(),
                'two_factor_confirmed_at' => null,
            ]
        );

        $this->info("Akun Admin BK siap: {$user->email} (wajib aktivasi 2FA saat login pertama).");

        return self::SUCCESS;
    }
}
