<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peringatan Laporan Darurat - {{ $schoolName }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border: 1px solid #fecdd3; border-radius: 12px; overflow: hidden;">
        <div style="background-color: #be123c; color: #ffffff; padding: 18px 24px;">
            <p style="margin: 0; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; font-weight: bold;">Peringatan Prioritas Darurat — HiOwy</p>
            <h1 style="margin: 6px 0 0; font-size: 18px;">Laporan Darurat Baru (#{{ $caseLabel }})</h1>
        </div>
        <div style="padding: 24px; font-size: 14px; line-height: 1.6;">
            <p style="margin-top: 0;">Halo Tim Guru BK <strong>{{ $schoolName }}</strong>,</p>
            <p>Sistem menerima satu laporan baru yang ditandai <strong>Darurat</strong> dan memerlukan perhatian segera:</p>
            <table style="width: 100%; border-collapse: collapse; margin: 16px 0; background: #f8fafc; border-radius: 8px;">
                <tr>
                    <td style="padding: 10px 14px; color: #64748b; width: 140px;">ID Kasus</td>
                    <td style="padding: 10px 14px; font-weight: bold;">#{{ $caseLabel }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 14px; color: #64748b;">Kategori</td>
                    <td style="padding: 10px 14px; font-weight: bold;">{{ $category }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 14px; color: #64748b;">Lokasi</td>
                    <td style="padding: 10px 14px;">{{ $location }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 14px; color: #64748b;">Waktu Masuk</td>
                    <td style="padding: 10px 14px;">{{ $submittedDate }}</td>
                </tr>
            </table>
            <p style="color: #475569; font-size: 13px;">
                Demi keamanan privasi siswa, isi cerita maupun identitas pelapor tidak dicantumkan dalam email notifikasi ini. Silakan masuk ke portal BK terenkripsi untuk membaca detailnya:
            </p>
            <p style="margin: 20px 0;">
                <a href="{{ $dashboardUrl }}" style="display: inline-block; background-color: #0f172a; color: #ffffff; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; font-size: 13px;">Buka Detail Laporan di Portal BK</a>
            </p>
        </div>
    </div>
</body>
</html>
