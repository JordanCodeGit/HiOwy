@props(['title', 'headline' => 'Ruang Aman & Terlindung', 'description' => '', 'pose' => 1, 'twoFactor' => false])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — HiOwy</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-owy.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-screen antialiased" x-data="authPage">
    <div class="auth-page-content">
        <main class="auth-frame">
            <aside class="auth-hero">
                <div>
                    <span class="auth-trust"><span class="material-symbols-outlined" aria-hidden="true">gpp_good</span> Akses Khusus Staf Sekolah</span>
                    <h2>{{ $headline }}</h2>
                    @if ($description)<p>{{ $description }}</p>@endif
                </div>
                <div class="auth-companion">
                    <x-auth.mascot :pose="$pose" />
                    @if ($twoFactor)
                        <ol class="auth-steps">
                            <li><span>1</span> Buka aplikasi autentikator</li>
                            <li><span>2</span> Pindai QR dan verifikasi kode</li>
                        </ol>
                    @else
                        <span class="auth-companion-label">Sahabat Pendamping</span>
                    @endif
                </div>
            </aside>
            <section class="auth-panel">
                <div class="auth-brand"><span class="material-symbols-outlined" aria-hidden="true">verified_user</span>HiOwy!</div>
                {{ $slot }}
            </section>
        </main>
    </div>
    <x-auth.notice />
</body>
</html>
