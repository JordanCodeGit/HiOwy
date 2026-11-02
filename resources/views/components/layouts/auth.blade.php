@props(['title', 'bodyClass', 'mediumFonts' => false])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — HiOwy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @if ($mediumFonts)
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    @else
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&amp;family=Plus+Jakarta+Sans:wght@400;600;700&amp;display=swap" rel="stylesheet">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ $bodyClass }}" x-data="authPage">
    <div class="auth-page-content">
        {{ $slot }}
    </div>
    <x-auth.notice />
</body>
</html>
