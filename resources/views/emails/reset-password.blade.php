<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemulihan Kata Sandi HiOwy</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 560px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05);">
                    <!-- Brand Header -->
                    <tr>
                        <td style="background-color: #0b1329; padding: 28px 32px; text-align: left;">
                            <span style="font-size: 24px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">HiOwy!</span>
                            <span style="display: inline-block; margin-left: 10px; background-color: rgba(255,255,255,0.15); color: #38bdf8; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 9999px;">Keamanan Staf</span>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 12px 0;">
                                Halo, {{ $user->name }}
                            </h1>
                            <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 20px 0;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun <strong>HiOwy</strong> Anda ({{ $user->email }}). Klik tombol di bawah untuk membuat kata sandi baru Anda:
                            </p>

                            <!-- Call to Action Box -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px 20px; text-align: center; margin-bottom: 24px;">
                                <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background-color: #0b1329; color: #ffffff; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 32px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                    Atur Ulang Kata Sandi &rarr;
                                </a>
                                <p style="font-size: 12px; color: #64748b; margin: 16px 0 0 0;">
                                    ⏱️ Tautan ini hanya berlaku selama <strong>15 menit</strong> dan hanya dapat digunakan satu kali.
                                </p>
                            </div>

                            <!-- Fallback Link -->
                            <div style="border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 20px;">
                                <p style="font-size: 12px; color: #64748b; margin: 0 0 6px 0;">
                                    Jika tombol di atas tidak berfungsi, salin dan buka tautan berikut di peramban Anda:
                                </p>
                                <p style="font-size: 11px; line-height: 1.4; color: #0284c7; word-break: break-all; margin: 0;">
                                    <a href="{{ $resetUrl }}" style="color: #0284c7; text-decoration: underline;">{{ $resetUrl }}</a>
                                </p>
                            </div>

                            <p style="font-size: 12px; line-height: 1.5; color: #94a3b8; margin: 24px 0 0 0;">
                                Jika Anda tidak merasa meminta pengaturan ulang kata sandi, abaikan email ini. Akun Anda tetap aman dan kata sandi Anda tidak akan berubah.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 32px; font-size: 11px; color: #94a3b8; text-align: center;">
                            Email otomatis dari Sistem Keamanan HiOwy &bull; Ruang Aman Konseling Siswa
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
