<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poster Panduan Pelaporan Siswa A4 Landscape — {{ $schoolName }}</title>
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
        }

        .no-print-bar {
            width: 100%;
            max-width: 297mm;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
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

        /* A4 Landscape Page Container (297mm x 210mm) */
        .a4-page {
            width: 297mm;
            height: 210mm;
            background: #ffffff;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            padding: 12mm 16mm 10mm 16mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* Decorative Background Elements */
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
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-top: 2mm;
        }

        .school-info {
            display: flex;
            align-items: center;
            gap: 14px;
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
        }

        .school-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .school-subtitle {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .portal-badge {
            background: #006398;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* Landscape Main Content: 2-column Grid */
        .landscape-content {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
            align-items: stretch;
            margin: 10px 0;
            flex: 1;
        }

        .qr-card-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 14px;
            text-align: center;
        }

        .qr-box {
            background: #ffffff;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
            display: inline-block;
            border: 1px solid #e2e8f0;
        }

        .qr-svg {
            width: 155px;
            height: 155px;
            display: block;
        }

        .url-link-text {
            margin-top: 8px;
            font-size: 11px;
            color: #64748b;
            word-break: break-all;
        }

        .url-link-text strong {
            color: #006398;
        }

        .right-section {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 10px;
        }

        .headline-section {
            text-align: left;
        }

        .headline-badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 14px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .headline-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
            margin-bottom: 4px;
        }

        .headline-desc {
            font-size: 12px;
            color: #475569;
            line-height: 1.45;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .step-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 8px;
            text-align: center;
        }

        .step-num {
            width: 26px;
            height: 26px;
            background: #006398;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 12px;
            margin: 0 auto 6px auto;
        }

        .step-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .step-desc {
            font-size: 10px;
            color: #64748b;
            line-height: 1.35;
        }

        .guarantee-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 8px 12px;
        }

        .guarantee-box span.material-symbols-outlined {
            color: #16a34a;
            font-size: 22px;
            flex-shrink: 0;
        }

        .guarantee-text {
            font-size: 11px;
            color: #166534;
            line-height: 1.35;
        }

        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10px;
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
                width: 297mm;
                height: 210mm;
                padding: 10mm 14mm 8mm 14mm;
                page-break-after: always;
            }
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('bk.manage', ['#qr']) }}" class="btn btn-outline">
                <span class="material-symbols-outlined">arrow_back</span>
                Kembali ke Kelola Sekolah
            </a>
            <span style="font-size: 13px; color: #64748b;">Format A4 Landscape Siap Cetak (300 DPI)</span>
        </div>
        <button onclick="window.print()" class="btn btn-primary">
            <span class="material-symbols-outlined">print</span>
            Cetak Poster / Simpan PDF (A4 Landscape)
        </button>
    </div>

    <div class="a4-page">
        <div class="decor-top-stripe"></div>

        <div class="header">
            <div class="school-info">
                <div class="school-icon">
                    <span class="material-symbols-outlined">school</span>
                </div>
                <div>
                    <h1 class="school-title">{{ $schoolName }}</h1>
                    <p class="school-subtitle">Layanan Bimbingan & Konseling (BK)</p>
                </div>
            </div>
            <div class="portal-badge">
                HiOwy! Portal Pengaduan Siswa
            </div>
        </div>

        <div class="landscape-content">
            <div class="qr-card-container">
                <div class="qr-box">
                    <div id="poster-qr-container"></div>
                </div>
                <p class="url-link-text">Tautan langsung: <br><strong>{{ $reportUrl }}</strong></p>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 6px;">Pindai dengan kamera ponsel</span>
            </div>

            <div class="right-section">
                <div class="headline-section">
                    <span class="headline-badge">Kanal Aman &amp; Terlindungi</span>
                    <h2 class="headline-title">Punya Masalah di Sekolah? Kamu Tidak Sendirian.</h2>
                    <p class="headline-desc">
                        Laporkan segala bentuk intimidasi, perundungan, pemalakan, atau masalah pribadi secara aman. Identitasmu dijamin terlindungi oleh ruang bimbingan konseling.
                    </p>
                </div>

                <div class="steps-grid">
                    <div class="step-item">
                        <div class="step-num">1</div>
                        <h4 class="step-title">Pindai Kode QR</h4>
                        <p class="step-desc">Arahkan kamera ponsel ke QR code tanpa perlu instal aplikasi.</p>
                    </div>
                    <div class="step-item">
                        <div class="step-num">2</div>
                        <h4 class="step-title">Isi Form Pengaduan</h4>
                        <p class="step-desc">Pilih kejadian &amp; lokasi. Kamu dapat melapor secara anonim.</p>
                    </div>
                    <div class="step-item">
                        <div class="step-num">3</div>
                        <h4 class="step-title">Didampingi Guru BK</h4>
                        <p class="step-desc">Konselor sekolah segera memvalidasi dan mendampingi kamu.</p>
                    </div>
                </div>

                <div class="guarantee-box">
                    <span class="material-symbols-outlined">verified_user</span>
                    <p class="guarantee-text">
                        <strong>Jaminan Kerahasiaan Penuh:</strong> Data dan identitas pelapor dilindungi berdasarkan Standar Perlindungan Anak di Satuan Pendidikan. Laporan ditujukan khusus untuk konselor sekolah.
                    </p>
                </div>
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
                        if (container) container.innerHTML = qr.createSvg(160, 4);
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
