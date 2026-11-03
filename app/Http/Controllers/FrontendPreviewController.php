<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class FrontendPreviewController extends Controller
{
    public function __invoke(string $page): RedirectResponse
    {
        [$route, $message, $nextRoute] = match ($page) {
            'login' => ['login', 'Login belum terintegrasi. Identitas dan kata sandi belum diverifikasi.', 'two-factor.challenge'],
            'signup' => ['register', 'Pendaftaran belum terintegrasi. Akun belum dibuat dan data tidak disimpan.', 'two-factor.setup'],
            'verify-two-factor' => ['two-factor.challenge', 'Verifikasi 2FA belum terintegrasi. Kode belum diperiksa dan akses belum diberikan.', 'bk.dashboard'],
            'forgot-password' => ['password.request', 'Pemulihan kata sandi belum terintegrasi. Email belum dikirim.', null],
            'school' => ['onboarding.school', 'Pengaturan sekolah belum terintegrasi. Data tidak disimpan. Anda dapat melanjutkan ke pratinjau Dashboard BK.', 'bk.dashboard'],
            'bk' => ['bk.dashboard', 'Tindakan ini belum terintegrasi dengan backend. Data tidak dikirim atau disimpan.', null],
            'kesiswaan' => ['kesiswaan.dashboard', 'Tindakan ini belum terintegrasi dengan backend. Data tidak dikirim atau disimpan.', null],
            'walas' => ['walas.dashboard', 'Tindakan ini belum terintegrasi dengan backend. Data tidak dikirim atau disimpan.', null],
            default => abort(404),
        };

        return redirect()->route($route)->with([
            'frontend_notice' => $message,
            'frontend_next_url' => $nextRoute ? route($nextRoute) : '',
        ]);
    }
}
