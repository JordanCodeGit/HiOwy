<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan formulir permintaan lupa kata sandi.
     */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim email berisi tautan reset kata sandi ke pengguna.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Alamat email tidak terdaftar dalam sistem sekolah kami.',
                ]);
        }

        // Generate token acak 64 karakter
        $token = Str::random(64);

        // Simpan token yang di-hash ke tabel password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token'      => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $resetUrl));
        } catch (\Throwable $e) {
            Log::warning('Email reset password gagal terkirim: ' . $e->getMessage());
        }

        Log::info("Password reset link generated for {$user->email}: {$resetUrl}");

        return back()->with([
            'status'    => 'Tautan pemulihan kata sandi telah dikirim ke ' . $user->email . '.',
            'email'     => $user->email,
            'reset_url' => $resetUrl,
        ]);
    }

    /**
     * Tampilkan formulir reset kata sandi baru.
     */
    public function showResetPassword(Request $request, string $token): View|RedirectResponse
    {
        $email = $request->query('email');

        if (! $email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Parameter email tidak ditemukan dalam tautan.',
            ]);
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Tautan pemulihan tidak valid atau sudah pernah digunakan.',
            ]);
        }

        // Validasi waktu kedaluwarsa 15 menit
        if (now()->diffInMinutes($record->created_at) > 15) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return redirect()->route('password.request')->withErrors([
                'email' => 'Tautan pemulihan telah kedaluwarsa (melebihi batas waktu 15 menit). Silakan ajukan ulang.',
            ]);
        }

        if (! Hash::check($token, $record->token)) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Token pemulihan tidak valid.',
            ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Simpan kata sandi baru pengguna ke database.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.mixed'     => 'Kata sandi harus kombinasi huruf besar dan kecil.',
            'password.numbers'   => 'Kata sandi harus mengandung minimal 1 angka.',
            'password.symbols'   => 'Kata sandi harus mengandung minimal 1 simbol khusus.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (! $record || ! Hash::check($request->token, $record->token) || now()->diffInMinutes($record->created_at) > 15) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Tautan pemulihan tidak valid atau sudah kedaluwarsa. Silakan ajukan ulang.',
            ]);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Pengguna tidak ditemukan dalam sistem.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token yang sudah berhasil dipakai
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with([
            'status' => 'Kata sandi berhasil diperbarui! Silakan masuk menggunakan kata sandi baru Anda.',
            'email'  => $user->email,
        ]);
    }
}
