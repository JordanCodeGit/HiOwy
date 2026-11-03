@props(['portal', 'page'])
@php
    $items = match ($portal) {
        'bk' => [['dashboard', 'inbox', 'Laporan'], ['manage', 'tune', 'Kelola'], ['statistics', 'bar_chart', 'Statistik']],
        'kesiswaan' => [['dashboard', 'inbox', 'Laporan'], ['archive', 'archive', 'Arsip'], ['statistics', 'bar_chart', 'Statistik'], ['access-log', 'history', 'Log']],
        'walas' => [['dashboard', 'forward_to_inbox', 'Ringkasan']],
    };
@endphp
<nav class="staff-bottom-nav" aria-label="Navigasi utama mobile">
    @foreach($items as [$key, $icon, $label])
        @php($active = $page === $key || ($key === 'dashboard' && in_array($page, ['report', 'summary'], true)))
        <a href="{{ route($portal.'.'.$key) }}" @if($active) aria-current="page" @endif>
            <span class="material-symbols-outlined" aria-hidden="true">{{ $icon }}</span><span>{{ $label }}</span>
        </a>
    @endforeach
    @if($portal === 'bk')
        <button type="button" data-staff-menu aria-controls="staff-more-menu" aria-expanded="false" @if(in_array($page, ['learn', 'archive', 'access-log'], true)) class="is-active" @endif><span class="material-symbols-outlined" aria-hidden="true">grid_view</span><span>Lainnya</span></button>
    @endif
</nav>
@if($portal === 'bk')
<dialog id="staff-more-menu" class="staff-sheet" aria-labelledby="staff-more-title">
    <div class="staff-sheet-heading"><h2 id="staff-more-title">Menu lainnya</h2><button type="button" data-close-sheet aria-label="Tutup menu"><span class="material-symbols-outlined">close</span></button></div>
    <nav class="staff-more-links" aria-label="Menu lainnya">
        @foreach([['learn', 'auto_stories', 'Halaman Pelajari'], ['archive', 'archive', 'Arsip'], ['access-log', 'history', 'Log Akses']] as [$key, $icon, $label])
        <a href="{{ route('bk.'.$key) }}" @if($page === $key) aria-current="page" @endif><span class="material-symbols-outlined">{{ $icon }}</span>{{ $label }}<span class="material-symbols-outlined">chevron_right</span></a>
        @endforeach
    </nav>
</dialog>
@endif
