<x-layouts.bk title="Ringkasan Diteruskan — Wali Kelas" page="dashboard" portal="walas" :report-count="$unreadCount" :notifications="$notifications">
    <main x-data="walasDashboard" data-report-list @bk-search.window="query = $event.detail.query; filter = 'all'" class="bg-background min-h-screen">
        <div class="p-4 lg:p-margin-lg max-w-7xl w-full mx-auto flex flex-col gap-space-xl">
            <header class="flex flex-col xl:flex-row xl:items-end justify-between gap-space-md">
                <div class="flex flex-col gap-space-xs max-w-2xl">
                    <span class="self-start inline-flex items-center gap-1.5 px-space-sm py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm"><span class="material-symbols-outlined text-[14px] text-on-tertiary-container">verified_user</span>Identitas Pelapor Dilindungi</span>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mt-1">Ringkasan Diteruskan</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant">Catatan pendampingan siswa terusan dari Guru BK untuk pemantauan wali kelas.</p>
                </div>
                <nav aria-label="Filter Ringkasan" class="inline-flex flex-wrap p-1 bg-surface-container-low rounded-xl self-start xl:self-auto shadow-xs gap-1">
                    @foreach(['unread' => ['Belum Dibaca', $unreadCount], 'read' => ['Sudah Dibaca', $summaries->where('status', 'read')->count()], 'all' => ['Semua', $summaries->count()]] as $key => [$label, $count])
                    <button type="button" @click="filter = '{{ $key }}'" :aria-pressed="filter === '{{ $key }}'" :class="{ 'bk-tab-active': filter === '{{ $key }}' }" class="tab-btn flex items-center gap-2 px-space-md py-space-xs rounded-lg text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-all"><span>{{ $label }}</span><span class="px-2 py-0.5 rounded-full bg-surface-container text-secondary text-label-sm font-label-sm">{{ $count }}</span></button>
                    @endforeach
                </nav>
            </header>
            <div class="relative max-w-lg"><label for="summary-search" class="sr-only">Cari ringkasan atau pengirim</label><span class="material-symbols-outlined absolute left-3 top-2.5 text-outline text-xl">search</span><input id="summary-search" type="search" x-model="query" placeholder="Cari ringkasan atau pengirim..." class="w-full rounded-xl border border-outline-variant/40 bg-white pl-10 pr-4 py-2.5 text-sm focus:ring-secondary"></div>
            <div class="flex flex-col gap-space-md" id="feed-container">
                @foreach($summaries as $item)
                <a href="{{ route('walas.summary', $item['id']) }}" data-summary-card data-status="{{ $item['status'] }}" x-show="visible({{ $loop->index }})" class="summary-card bk-hover-card group relative block bg-surface-container-lowest rounded-xl p-space-lg shadow-xs hover:shadow-md transition-all duration-200 focus-visible:outline-2 focus-visible:outline-secondary">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs pb-space-sm mb-space-sm">
                        <div class="flex items-center gap-space-sm">
                            @if($item['status'] === 'unread')<span class="inline-flex rounded-full h-2.5 w-2.5 bg-secondary shrink-0" aria-hidden="true"></span>@else<span class="material-symbols-outlined text-[18px] text-on-tertiary-container" aria-hidden="true">done_all</span>@endif
                            <div class="flex flex-wrap items-center gap-2"><span class="font-title-md text-title-md text-on-surface">{{ $item['counselor'] }}</span><span class="inline-flex items-center px-2 py-0.5 rounded-full bg-surface-container-low text-on-secondary-container font-label-sm text-label-sm">{{ $item['role'] }}</span></div>
                        </div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1 shrink-0"><span class="material-symbols-outlined text-[15px]">schedule</span>{{ $item['time'] }}</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant line-clamp-2 leading-relaxed">{{ $item['excerpt'] }}</p>
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-space-md mt-space-sm">
                        @if($item['status'] === 'unread')<span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-secondary bg-surface-container-low px-2 py-0.5 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>Baru</span>@else<span class="font-label-sm text-label-sm text-on-surface-variant">Dibaca: {{ $item['read_at'] }}</span>@endif
                        <span class="flex items-center gap-1 text-on-surface group-hover:text-secondary font-label-lg text-label-lg transition-colors">Buka Catatan Lengkap<span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-0.5">chevron_right</span></span>
                    </div>
                </a>
                @endforeach
            </div>
            <div x-cloak x-show="visibleCount === 0" class="rounded-xl bg-white p-10 text-center"><span class="material-symbols-outlined text-4xl text-secondary">inbox</span><h2 class="mt-3 font-title-lg text-title-lg">Tidak ada ringkasan yang sesuai</h2><p class="mt-2 text-sm text-on-surface-variant">Coba pilihan tab atau kata pencarian lain.</p></div>
            <p class="text-center text-xs text-on-surface-variant">Menampilkan <span x-text="visibleCount"></span> dari {{ $summaries->count() }} ringkasan contoh · Kelas 8B</p>
        </div>
    </main>
</x-layouts.bk>
