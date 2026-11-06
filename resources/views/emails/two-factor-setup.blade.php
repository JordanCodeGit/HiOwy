<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi 2FA HiOwy</title>
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
                            <span style="display: inline-block; margin-left: 10px; background-color: rgba(255,255,255,0.15); color: #38bdf8; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 9999px;">Akses Khusus Staf</span>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 12px 0;">
                                Halo, {{ $user->name }}
                            </h1>
                            <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 20px 0;">
                                Anda masuk untuk pertama kali ke sistem <strong>HiOwy</strong>. Untuk menjaga keamanan data siswa dan kasus konseling, Anda diwajibkan menghubungkan akun ke aplikasi <strong>Google Authenticator</strong>.
                            </p>

                            <!-- Call to Action Box -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px;">
                                <p style="font-size: 13px; font-weight: 600; color: #334155; margin: 0 0 16px 0;">
                                    Buka email ini di ponsel Anda, lalu ketuk tombol di bawah:
                                </p>
                                <a href="{{ $activationUrl }}" target="_blank" style="display: inline-block; background-color: #0b1329; color: #ffffff; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 28px; border-radius: 10px;">
                                    Hubungkan ke Authenticator &rarr;
                                </a>
                                <p style="font-size: 11px; color: #64748b; margin: 14px 0 0 0;">
                                    Tautan ini berlaku selama 15 menit dan hanya dapat digunakan sekali.
                                </p>
                            </div>

                            <!-- Manual Key Fallback -->
                            <div style="border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 20px;">
                                <p style="font-size: 12px; color: #64748b; margin: 0 0 6px 0;">
                                    Jika Anda membuka email ini di laptop, Anda bisa memasukkan kunci manual ini ke aplikasi Google Authenticator:
                                </p>
                                <div style="background-color: #f1f5f9; padding: 8px 12px; border-radius: 6px; font-family: monospace; font-size: 13px; font-weight: 700; color: #0f172a; letter-spacing: 1px; display: inline-block;">
                                    {{ $user->two_factor_secret }}
                                </div>
                            </div>

                            <p style="font-size: 12px; line-height: 1.5; color: #94a3b8; margin: 24px 0 0 0;">
                                Setelah akun ditambahkan ke authenticator, masukkan 6 digit kode yang tampil ke halaman verifikasi HiOwy Anda.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 32px; font-size: 11px; color: #94a3b8; text-align: center;">
                            Email otomatis dari Sistem Keamanan HiOwy &bull; Jangan teruskan email ini kepada siapapun.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
