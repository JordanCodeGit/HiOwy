<x-layouts.bk title="Ringkasan Diteruskan — Wali Kelas" page="dashboard" portal="walas" :report-count="$unreadCount" :notifications="$notifications">
    <main x-data="walasDashboard({ reportUrl: '{{ $reportUrl }}', qrScanUrl: '{{ $qrScanUrl }}', className: '{{ $className ?? '7B' }}' })" data-report-list @bk-search.window="query = $event.detail.query; filter = 'all'" class="bg-background min-h-screen">
        <div class="p-4 lg:p-margin-lg max-w-7xl w-full mx-auto flex flex-col gap-space-xl">
            <!-- Header Section -->
            <header class="flex flex-col xl:flex-row xl:items-end justify-between gap-space-md">
                <div class="flex flex-col gap-space-xs max-w-2xl">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-space-sm py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">
                            <span class="material-symbols-outlined text-[14px] text-on-tertiary-container">verified_user</span>
                            Identitas Pelapor Dilindungi
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-space-sm py-0.5 rounded-full bg-blue-50 text-blue-700 font-label-sm text-label-sm font-semibold border border-blue-200">
                            <span class="material-symbols-outlined text-[14px]">school</span>
                            Unit Kelas {{ $className ?? '7B' }}
                        </span>
                    </div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mt-1">Ringkasan Diteruskan</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant">Catatan pendampingan siswa terusan dari Guru BK untuk pemantauan wali kelas binaan.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 self-start xl:self-auto">
                    <!-- QR Quick Access Button -->
                    <button type="button" @click="qrModalOpen = true" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-slate-800 font-label-md text-label-md font-semibold border border-slate-200/80 shadow-2xs transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-[18px] text-blue-600">qr_code_2</span>
                        <span>QR Aduan Kelas</span>
                    </button>
                    <a href="{{ route('walas.poster') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-label-md text-label-md font-semibold shadow-xs transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        <span>Cetak Poster Kelas</span>
                    </a>

                    <!-- Filter Tabs -->
                    <nav aria-label="Filter Ringkasan" class="inline-flex flex-wrap p-1 bg-surface-container-low rounded-xl shadow-xs gap-1">
                        @foreach(['unread' => ['Belum Dibaca', $unreadCount], 'read' => ['Sudah Dibaca', $readCount ?? $summaries->where('status', 'read')->count()], 'all' => ['Semua', $summaries->count()]] as $key => [$label, $count])
                        <button type="button" @click="filter = '{{ $key }}'" :aria-pressed="filter === '{{ $key }}'" :class="{ 'bk-tab-active': filter === '{{ $key }}' }" class="tab-btn flex items-center gap-2 px-space-md py-space-xs rounded-lg text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg transition-all cursor-pointer">
                            <span>{{ $label }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-surface-container text-secondary text-label-sm font-label-sm">{{ $count }}</span>
                        </button>
                        @endforeach
                    </nav>
                </div>
            </header>

            <!-- Search Bar -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="relative max-w-lg w-full">
                    <label for="summary-search" class="sr-only">Cari ringkasan atau pengirim</label>
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-outline text-xl">search</span>
                    <input id="summary-search" type="search" x-model="query" placeholder="Cari ringkasan, guru BK, arahan observasi..." class="w-full rounded-xl border border-outline-variant/40 bg-white pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                </div>

                <div class="text-xs text-on-surface-variant flex items-center gap-1.5 self-end sm:self-center">
                    <span class="material-symbols-outlined text-[16px] text-slate-400">info</span>
                    <span>Hanya menampilkan ringkasan terusan khusus <strong>Kelas {{ $className ?? '7B' }}</strong></span>
                </div>
            </div>

            <!-- Feed List of Summaries -->
            <div class="flex flex-col gap-space-md" id="feed-container">
                @forelse($summaries as $item)
                <div data-summary-card data-id="{{ $item['id'] }}" data-status="{{ $item['status'] }}" x-show="visible({{ $loop->index }})" class="summary-card bk-hover-card group relative block bg-surface-container-lowest rounded-xl p-space-lg shadow-xs hover:shadow-md transition-all duration-200 border border-slate-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs pb-space-sm mb-space-sm border-b border-slate-100/80">
                        <div class="flex items-center gap-space-sm">
                            @if($item['status'] === 'unread')
                                <span class="inline-flex rounded-full h-2.5 w-2.5 bg-blue-600 shrink-0 animate-pulse" title="Belum dibaca"></span>
                            @else
                                <span class="material-symbols-outlined text-[18px] text-emerald-600" title="Sudah dibaca">done_all</span>
                            @endif
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-title-md text-title-md text-on-surface font-semibold">{{ $item['counselor'] }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-surface-container-low text-on-secondary-container font-label-sm text-label-sm">{{ $item['role'] }}</span>
                            </div>
                        </div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1 shrink-0">
                            <span class="material-symbols-outlined text-[15px]">schedule</span>
                            {{ $item['time'] }}
                        </span>
                    </div>

                    <a href="{{ route('walas.summary', $item['id']) }}" class="block focus:outline-none">
                        <p class="font-body-md text-body-md text-on-surface-variant line-clamp-2 leading-relaxed hover:text-slate-900 transition-colors">
                            {{ $item['excerpt'] }}
                        </p>
                    </a>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-space-md mt-space-sm">
                        <div class="flex items-center gap-2">
                            @if($item['status'] === 'unread')
                                <span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-blue-700 bg-blue-50 border border-blue-100 px-2.5 py-0.5 rounded-full font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    Baru
                                </span>
                            @else
                                <span class="font-label-sm text-label-sm text-slate-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-emerald-600">check</span>
                                    Dibaca: {{ $item['read_at'] }}
                                </span>
                            @endif

                            <!-- Quick Toggle Read Button -->
                            <button type="button" @click="quickToggleRead({{ $item['id'] }}, $event)" class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-1 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors cursor-pointer" title="Ubah status baca ringkasan ini">
                                <span class="material-symbols-outlined text-[15px]">{{ $item['status'] === 'read' ? 'mark_email_unread' : 'mark_email_read' }}</span>
                                <span>{{ $item['status'] === 'read' ? 'Tandai Belum Dibaca' : 'Tandai Dibaca' }}</span>
                            </button>
                        </div>

                        <a href="{{ route('walas.summary', $item['id']) }}" class="flex items-center gap-1 text-on-surface group-hover:text-secondary font-label-lg text-label-lg transition-colors font-semibold">
                            <span>Buka Catatan Lengkap</span>
                            <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-0.5">chevron_right</span>
                        </a>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl p-8 border border-slate-200/80 text-center flex flex-col items-center justify-center gap-2">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                        <span class="material-symbols-outlined text-[24px]">inbox</span>
                    </div>
                    <span class="text-sm font-bold text-slate-800">Belum Ada Ringkasan Binaan</span>
                    <p class="text-xs text-slate-500 max-w-sm">Ringkasan aman dari konselor BK untuk siswa kelas Anda akan muncul di sini setelah diteruskan oleh Guru BK.</p>
                </div>
                @endforelse
            </div>

            <!-- Empty State -->
            <div x-cloak x-show="visibleCount === 0" class="rounded-xl bg-white p-10 text-center border border-slate-100 shadow-xs">
                <span class="material-symbols-outlined text-4xl text-secondary">inbox</span>
                <h2 class="mt-3 font-title-lg text-title-lg font-bold text-slate-900">Tidak ada ringkasan yang sesuai</h2>
                <p class="mt-2 text-sm text-on-surface-variant">Coba pilihan tab atau kata pencarian lain.</p>
            </div>

            <!-- Footer Counter -->
            <p class="text-center text-xs text-on-surface-variant">Menampilkan <span x-text="visibleCount"></span> dari {{ $summaries->count() }} ringkasan terusan · Kelas {{ $className ?? '7B' }}</p>

            <!-- Classroom QR Code Section (#qr anchor) -->
            <section id="qr" class="mt-8 bg-surface-container-lowest rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex flex-col gap-3 max-w-xl">
                    <div class="flex items-center gap-2 text-blue-600 font-label-sm uppercase tracking-wider font-semibold">
                        <span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
                        <span>Kanal Aduan Siswa — Ruang Kelas {{ $className ?? '7B' }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Pasang Kode QR Resmi di Mading Kelas</h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Siswa di kelas binaan Anda dapat memindai kode QR resmi ini dari ponsel untuk menyampaikan aduan atau perundungan secara terenkripsi dan langsung diterima oleh Guru BK tanpa rasa takut.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button type="button" @click="qrModalOpen = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[17px]">fullscreen</span>
                            <span>Buka Layar Penuh QR</span>
                        </button>
                        <a href="{{ route('walas.poster') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold border border-slate-200 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[17px]">picture_as_pdf</span>
                            <span>Unduh Poster A4 Siap Cetak</span>
                        </a>
                        <button type="button" @click="copyReportUrl()" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[17px]">content_copy</span>
                            <span>Salin Tautan</span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col items-center p-4 bg-slate-50 rounded-2xl border border-slate-200 shrink-0 text-center">
                    <div id="walas-qr-svg" class="p-3 bg-white rounded-xl shadow-xs border border-slate-100" x-html="qrSvgHtml">
                        <!-- SVG QR Code rendered by Alpine -->
                    </div>
                    <span class="text-xs font-bold text-slate-800 mt-2.5">{{ $schoolName }}</span>
                    <span class="text-[11px] text-slate-500 font-medium">Ruang Kelas {{ $className ?? '7B' }}</span>
                </div>
            </section>
        </div>

        <!-- Fullscreen QR Modal -->
        <div x-cloak x-show="qrModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto" @keydown.escape.window="qrModalOpen = false">
            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 flex flex-col items-center gap-5 my-auto" @click.outside="qrModalOpen = false">
                <!-- Close Button -->
                <button type="button" @click="qrModalOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors p-1.5 rounded-lg hover:bg-slate-100 cursor-pointer">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>

                <div class="text-center">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold mb-2">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        Kanal Aduan Resmi Sekolah
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $schoolName }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kode QR Pelaporan untuk Siswa Kelas {{ $className ?? '7B' }}</p>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col items-center justify-center">
                    <div class="p-4 bg-white rounded-xl shadow-xs border border-slate-100" x-html="qrSvgHtml"></div>
                    <p class="text-[11px] text-slate-400 mt-2 font-mono">Arahkan kamera ponsel siswa ke kode QR</p>
                </div>

                <div class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-2">
                    <span class="text-xs font-mono text-slate-700 truncate" x-text="reportUrl">{{ $reportUrl }}</span>
                    <button type="button" @click="copyReportUrl()" class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors shrink-0 cursor-pointer" title="Salin tautan">
                        <span class="material-symbols-outlined text-base">content_copy</span>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3 w-full">
                    <a href="{{ route('walas.poster') }}" target="_blank" class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-all shadow-xs text-center cursor-pointer">
                        <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                        <span>Cetak Poster A4</span>
                    </a>
                    <button type="button" @click="downloadQrPng()" class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition-all text-center cursor-pointer border border-slate-200">
                        <span class="material-symbols-outlined text-base">image</span>
                        <span>Unduh PNG</span>
                    </button>
                </div>

                <button type="button" @click="window.open(qrScanUrl, '_blank')" class="text-xs text-slate-500 hover:text-blue-600 flex items-center gap-1 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                    <span>Uji Coba Pindai QR (+1 Pemindaian)</span>
                </button>
            </div>
        </div>
    </main>
</x-layouts.bk>
