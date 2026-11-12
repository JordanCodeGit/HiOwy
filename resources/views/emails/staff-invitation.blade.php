<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Staf Unit Sekolah — HiOwy</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05);">
                    <!-- Brand Header -->
                    <tr>
                        <td style="background-color: #006398; padding: 28px 32px; text-align: left;">
                            <span style="font-size: 26px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">HiOwy!</span>
                            <span style="display: inline-block; margin-left: 12px; background-color: rgba(255,255,255,0.2); color: #ffffff; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px;">Undangan Staf Sekolah</span>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 32px;">
                            <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 12px 0;">
                                Halo, {{ $staff->name }}
                            </h1>
                            <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 20px 0;">
                                Anda telah diundang secara resmi oleh Tim Bimbingan &amp; Konseling (BK) <strong>{{ $schoolName }}</strong> untuk bergabung ke portal pendampingan siswa <strong>HiOwy</strong>.
                            </p>

                            <!-- Role Card -->
                            <div style="background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 18px 20px; margin-bottom: 24px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30%" style="font-size: 12px; color: #0284c7; font-weight: 700; text-transform: uppercase;">Peran Akses</td>
                                        <td style="font-size: 14px; font-weight: 700; color: #0369a1;">
                                            @if($staff->role === 'walikelas')
                                                Wali Kelas {{ $staff->class_name ?? 'Binaan' }}
                                            @else
                                                Tim Kesiswaan (Eskalasi Kasus)
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; color: #0284c7; font-weight: 700; text-transform: uppercase; padding-top: 8px;">Unit Sekolah</td>
                                        <td style="font-size: 14px; color: #334155; padding-top: 8px;">{{ $schoolName }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; color: #0284c7; font-weight: 700; text-transform: uppercase; padding-top: 8px;">Alamat Email</td>
                                        <td style="font-size: 14px; color: #334155; padding-top: 8px;">{{ $staff->email }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- CTA Box -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px 20px; text-align: center; margin-bottom: 24px;">
                                <a href="{{ $invitationUrl }}" target="_blank" style="display: inline-block; background-color: #006398; color: #ffffff; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 32px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                    Terima Undangan &amp; Masuk Portal &rarr;
                                </a>
                                <p style="font-size: 12px; color: #64748b; margin: 16px 0 0 0;">
                                    ⏱️ Tautan aktivasi berlaku selama <strong>7 hari</strong>.
                                </p>
                            </div>

                            <!-- Instructions -->
                            <p style="font-size: 13px; line-height: 1.6; color: #475569; margin: 0 0 16px 0;">
                                Sebagai staf yang terdaftar, Anda dapat memantau status laporan siswa binaan, berkoordinasi langsung dengan guru BK, dan mendukung terciptanya lingkungan sekolah yang aman dari perundungan.
                            </p>

                            <!-- Fallback Link -->
                            <div style="border-top: 1px solid #e2e8f0; padding-top: 18px; margin-top: 20px;">
                                <p style="font-size: 12px; color: #64748b; margin: 0 0 6px 0;">
                                    Jika tombol di atas tidak dapat diklik, salin dan buka tautan berikut di browser Anda:
                                </p>
                                <p style="font-size: 11px; line-height: 1.4; color: #0284c7; word-break: break-all; margin: 0;">
                                    <a href="{{ $invitationUrl }}" style="color: #0284c7; text-decoration: underline;">{{ $invitationUrl }}</a>
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 32px; font-size: 11px; color: #94a3b8; text-align: center;">
                            Diterbitkan oleh Tim Bimbingan Konseling (BK) {{ $schoolName }} melalui platform HiOwy!
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
