@props(['title', 'page', 'mode' => '', 'portal' => 'bk', 'reportCount' => 14, 'notifications' => []])

<!DOCTYPE html>
<html lang="id" class="bg-background">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — HiOwy!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bk-shell min-h-screen bg-background text-on-surface antialiased font-body-md" data-staff-portal="{{ $portal }}" data-staff-page="{{ $page }}" data-staff-login="{{ route('login') }}" x-data="bkShell" @keydown.escape.window="closeOverlays()">
    <a class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:bg-white focus:p-3" href="#bk-content">Lewati navigasi</a>
    <x-bk.sidebar :page="$page" :portal="$portal" :report-count="$reportCount" />
    <div class="min-w-0 lg:pl-64">
        <x-bk.header :portal="$portal" :notifications="$notifications" />
        <div id="bk-content" tabindex="-1" class="bk-content {{ $mode === 'dashboard' ? 'bk-dashboard-theme' : '' }}">
            <div class="bk-preview-label px-4 pt-3 text-[10px] text-slate-500 lg:px-8">Pratinjau frontend · Data contoh desain · Perubahan belum disimpan</div>
            {{ $slot }}
        </div>
    </div>
    <x-bk.mobile-nav :portal="$portal" :page="$page" />
    <x-auth.notice />
    <noscript><p class="fixed bottom-0 inset-x-0 z-50 bg-amber-100 p-3 text-center text-sm">Aktifkan JavaScript untuk interaksi pratinjau.</p></noscript>
</body>
</html>
