<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poster Panduan Pelaporan Siswa A4 Portrait — {{ $schoolName }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-owy.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            width: 100%;
            max-width: 210mm;
            background: #ffffff;
            padding: 14px 18px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background: #006398;
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #004c75;
        }

        .btn-outline {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-outline:hover {
            background: #e2e8f0;
        }

        /* A4 Portrait Page Container (210mm x 297mm) */
        .a4-page {
            width: 210mm;
            height: 297mm;
            background: #ffffff;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            padding: 14mm 15mm 11mm 15mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .decor-top-stripe {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6mm;
            background: linear-gradient(90deg, #006398 0%, #38bdf8 100%);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            margin-top: 2mm;
        }

        .school-info {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .school-icon {
            width: 48px;
            height: 48px;
            background: #e0f2fe;
            color: #006398;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .school-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .school-subtitle {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 2px;
        }

        .portal-badge {
            background: #006398;
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* Portrait Vertical Main Stack */
        .portrait-content {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: space-between;
            gap: 16px;
            flex: 1;
            margin: 14px 0 12px 0;
        }

        .headline-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 14px 18px;
        }

        .headline-text {
            flex: 1;
            min-width: 0;
        }

        .headline-badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .headline-title {
            font-size: 23px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .headline-desc {
            font-size: 12.5px;
            color: #475569;
            line-height: 1.45;
        }

        .mascot-img {
            width: 96px;
            height: 96px;
            object-fit: contain;
            flex-shrink: 0;
        }

        /* Center Focal Point: Large QR Card */
        .qr-hero-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%);
            border: 2.5px dashed #38bdf8;
            border-radius: 22px;
            padding: 22px 20px 18px 20px;
            text-align: center;
        }

        .qr-cta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #006398;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            padding: 5px 16px;
            border-radius: 999px;
            margin-bottom: 14px;
        }

        .qr-box {
            background: #ffffff;
            padding: 16px;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0, 99, 152, 0.12);
            border: 2px solid #bae6fd;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        #poster-qr-container svg {
            width: 300px;
            height: 300px;
            display: block;
        }

        .qr-caption {
            margin-top: 12px;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .url-link-text {
            margin-top: 4px;
            font-size: 12px;
            color: #475569;
            word-break: break-all;
        }

        .url-link-text strong {
            color: #006398;
            font-weight: 700;
        }

        /* 3 Steps Section Below QR */
        .steps-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .steps-heading {
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            color: #006398;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .step-item {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 12px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .step-num {
            width: 32px;
            height: 32px;
            background: #006398;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .step-title {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .step-desc {
            font-size: 11px;
            color: #64748b;
            line-height: 1.4;
        }

        .guarantee-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 10px 14px;
        }

        .guarantee-box span.material-symbols-outlined {
            color: #16a34a;
            font-size: 24px;
            flex-shrink: 0;
        }

        .guarantee-text {
            font-size: 11.5px;
            color: #166534;
            line-height: 1.4;
        }

        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10.5px;
            color: #94a3b8;
        }

        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .a4-page {
                box-shadow: none;
                margin: 0;
                width: 210mm;
                height: 297mm;
                padding: 13mm 14mm 10mm 14mm;
                break-inside: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    @php
        $resolvedBackUrl = $backUrl ?? (Auth::user()?->normalizedRole() === 'walas' ? route('walas.dashboard') : route('bk.manage').'#qr');
        $resolvedBackLabel = $backLabel ?? (Auth::user()?->normalizedRole() === 'walas' ? 'Kembali ke Dashboard Wali Kelas' : 'Kembali ke Kelola Sekolah');
    @endphp
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ $resolvedBackUrl }}" class="btn btn-outline">
                <span class="material-symbols-outlined">arrow_back</span>
                {{ $resolvedBackLabel }}
            </a>
            <span style="font-size: 13px; color: #64748b;">Format A4 Portrait Siap Cetak (300 DPI)</span>
        </div>
        <button onclick="window.print()" class="btn btn-primary">
            <span class="material-symbols-outlined">print</span>
            Cetak Poster / Simpan PDF (A4 Portrait)
        </button>
    </div>

    <div class="a4-page">
        <div class="decor-top-stripe"></div>

        <div class="header">
            <div class="school-info">
                <div class="school-icon">
                    @if(!empty($schoolConfig['school_logo_url']))
                        <img src="{{ $schoolConfig['school_logo_url'] }}" alt="{{ $schoolName }}" style="width: 40px; height: 40px; object-fit: contain;">
                    @else
                        <span class="material-symbols-outlined">school</span>
                    @endif
                </div>
                <div>
                    <h1 class="school-title">{{ $schoolName }}</h1>
                    <p class="school-subtitle">Layanan Bimbingan &amp; Konseling (BK) @if(!empty($schoolConfig['emergency_contact'])) • Kontak Darurat: {{ $schoolConfig['emergency_contact'] }} @endif</p>
                </div>
            </div>
            <div class="portal-badge">
                HiOwy! Portal Pengaduan Siswa
            </div>
        </div>

        <div class="portrait-content">
            <!-- Headline + Maskot Owy -->
            <div class="headline-banner">
                <div class="headline-text">
                    <span class="headline-badge">Kanal Aman &amp; Terlindungi</span>
                    <h2 class="headline-title">Punya Masalah di Sekolah? Kamu Tidak Sendirian.</h2>
                    <p class="headline-desc">
                        Laporkan perundungan, intimidasi, pemalakan, atau curhat secara aman bersama Owy &amp; Guru BK. Jam baca layanan BK: <strong>{{ $schoolConfig['reading_hours'] ?? 'Senin–Jumat, 07.00–15.00 WIB' }}</strong>.
                    </p>
                </div>
                <img src="{{ asset('maskot_owy/maskot_6.png') }}" alt="Maskot Owy" class="mascot-img">
            </div>

            <!-- QR Code Besar di Tengah -->
            <div class="qr-hero-card">
                <span class="qr-cta-pill">
                    <span class="material-symbols-outlined" style="font-size: 16px;">qr_code_scanner</span>
                    <span>Pindai Kode QR di Sini untuk Melapor</span>
                </span>
                <div class="qr-box">
                    <div id="poster-qr-container"></div>
                </div>
                <p class="qr-caption">Arahkan kamera ponselmu ke kode QR — tanpa perlu instal aplikasi</p>
                <p class="url-link-text">Atau buka tautan langsung: <strong>{{ $reportUrl }}</strong></p>
            </div>

            <!-- Tahapan Pelaporan di Bawah QR -->
            <div class="steps-section">
                <p class="steps-heading">3 Langkah Mudah Melapor Bersama Owy</p>
                <div class="steps-grid">
                    <div class="step-item">
                        <div class="step-num">1</div>
                        <h4 class="step-title">Pindai Kode QR</h4>
                        <p class="step-desc">Arahkan kamera ponsel ke kode QR di atas tanpa perlu mengunduh aplikasi.</p>
                    </div>
                    <div class="step-item">
                        <div class="step-num">2</div>
                        <h4 class="step-title">Isi Form Pengaduan</h4>
                        <p class="step-desc">Pilih jenis kejadian, lokasi, dan ceritakan kondisimu. Kamu dapat melapor secara anonim.</p>
                    </div>
                    <div class="step-item">
                        <div class="step-num">3</div>
                        <h4 class="step-title">Didampingi Guru BK</h4>
                        <p class="step-desc">Simpan kode tiket &amp; passwordmu. Guru BK segera menelaah dan mendampingi kamu.</p>
                    </div>
                </div>
            </div>

            <!-- Jaminan Kerahasiaan -->
            <div class="guarantee-box">
                <span class="material-symbols-outlined">verified_user</span>
                <p class="guarantee-text">
                    <strong>Jaminan Kerahasiaan &amp; Perlindungan:</strong> Laporan terenkripsi dan dijaga sesuai Standar Perlindungan Anak di Satuan Pendidikan. Laporan ditujukan khusus bagi tim konselor sekolah.
                </p>
            </div>
        </div>

        <div class="footer">
            <span>Diterbitkan resmi oleh Bimbingan Konseling (BK) {{ $schoolName }}</span>
            <span>Didukung oleh Platform HiOwy!</span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const render = () => {
                if (window.QRCode) {
                    try {
                        const qr = new window.QRCode(0, 'M');
                        qr.addData('{{ $qrScanUrl }}');
                        qr.make();
                        const container = document.getElementById('poster-qr-container');
                        if (container) container.innerHTML = qr.createSvg(300, 4);
                    } catch (e) {
                        console.error('Error generating poster QR:', e);
                    }
                } else {
                    setTimeout(render, 50);
                }
            };
            render();
        });
    </script>
</body>
</html>
