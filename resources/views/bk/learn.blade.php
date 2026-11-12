<x-layouts.bk title="Halaman Pelajari" page="learn" mode="">
<main x-data="bkLearn" class="relative w-full min-h-screen bg-background">
<div class="flex flex-col w-full">
<div class="px-8 py-8 flex flex-col gap-8 max-w-7xl mx-auto w-full">

<!-- Floating Toast Notification -->
<div x-show="showToast" x-cloak x-transition.opacity class="fixed top-20 right-8 z-50 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-700 text-xs font-semibold">
    <span class="material-symbols-outlined text-emerald-400 text-lg">check_circle</span>
    <span x-text="toastMessage"></span>
</div>

<!-- Header Page -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm uppercase tracking-wider">Modul Kurikulum BK</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Manajemen Halaman Pelajari</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Kelola materi edukasi pencegahan dan pengumuman resmi BK untuk portal siswa.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('public.learn', ['code' => 'smakita']) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-surface-container-high bg-surface-container-lowest hover:bg-surface-container-low text-on-surface font-label-md text-label-md shadow-xs transition-all cursor-pointer">
            <span class="material-symbols-outlined text-lg text-secondary">visibility</span>
            <span>Lihat Portal Siswa</span>
        </a>
        <button @click="newAnnouncement()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-label-md text-label-md shadow-xs hover:bg-primary-container transition-all cursor-pointer" id="btn-focus-form" type="button">
            <span class="material-symbols-outlined text-lg">add_circle</span>
            <span>Tambah Pengumuman</span>
        </button>
    </div>
</div>

<!-- Flash Alerts -->
@if(session('success_message'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 flex items-center justify-between text-sm shadow-xs">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-xl">check_circle</span>
            <span class="font-medium">{{ session('success_message') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer">
            <span class="material-symbols-outlined text-base">close</span>
        </button>
    </div>
@endif

@if(session('error_message') || $errors->any())
    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-700 flex items-center justify-between text-sm shadow-xs">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-xl">error</span>
            <span class="font-medium">{{ session('error_message') ?: $errors->first() }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-800 cursor-pointer">
            <span class="material-symbols-outlined text-base">close</span>
        </button>
    </div>
@endif

@if(!$hasTable)
    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-800 flex items-start gap-3 text-sm shadow-xs">
        <span class="material-symbols-outlined text-xl text-amber-600 shrink-0 mt-0.5">info</span>
        <div class="flex flex-col gap-1">
            <span class="font-bold">Mode Pratinjau Sementara Aktif</span>
            <span class="text-xs text-amber-700">Tabel database <code>learning_materials</code> belum dibuat di MySQL lokal Anda. Jalankan perintah <code>php artisan migrate</code> di terminal Anda agar pengumuman tersimpan permanen di database.</span>
        </div>
    </div>
@endif

<!-- Stats Row: Total Pembaca Siswa & Artikel Terbit -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <!-- Card 1: Total Pembaca Siswa -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-surface-container/60 flex items-center justify-between">
        <div class="flex flex-col gap-1.5">
            <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Total Pembaca Siswa</span>
            <div class="flex items-baseline gap-2.5">
                <span class="font-headline-md text-headline-md font-bold text-on-surface tracking-tight">{{ number_format($totalViews) }}</span>
                <span class="font-label-sm text-label-sm text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Tayangan Aktif</span>
            </div>
            <span class="text-xs text-slate-400">Total pembaca kumulatif di portal siswa</span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
            <span class="material-symbols-outlined text-3xl">visibility</span>
        </div>
    </div>

    <!-- Card 2: Artikel Terbit -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-surface-container/60 flex items-center justify-between">
        <div class="flex flex-col gap-1.5">
            <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Artikel Terbit</span>
            <div class="flex items-baseline gap-2.5">
                <span class="font-headline-md text-headline-md font-bold text-on-surface tracking-tight">{{ $publishedCount }}</span>
                <span class="font-label-sm text-label-sm text-slate-600 font-semibold">Modul Aktif</span>
            </div>
            <span class="text-xs text-slate-400">{{ $totalCount - $publishedCount }} materi tersimpan dalam draf disembunyikan</span>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
            <span class="material-symbols-outlined text-3xl">campaign</span>
        </div>
    </div>
</div>

<!-- Main 2-Column Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

    <!-- LEFT COLUMN: Form Pengumuman & Materi (5 cols) -->
    <div class="lg:col-span-5 flex flex-col gap-4 sticky top-24">
        <div class="bk-hover-card rounded-2xl bg-surface-container-lowest shadow-xs p-6 flex flex-col gap-6">
            <div class="flex items-center justify-between pb-4 border-b border-surface-container">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-6 rounded-full bg-secondary inline-block"></span>
                    <div class="flex flex-col">
                        <h2 class="font-title-lg text-title-lg text-on-surface font-bold">Form Materi &amp; Edukasi</h2>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span x-text="isEditing ? 'Mode Ubah: ' + editingCode : 'Draft Baru'" :class="isEditing ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-surface-container-low text-on-surface-variant border-surface-container'" class="px-2.5 py-1 rounded-full font-label-sm text-label-sm flex items-center gap-1 font-semibold border"></span>
                    <button type="button" x-show="isEditing" x-cloak @click="resetAnnouncement()" class="text-xs text-slate-500 hover:text-slate-800 underline cursor-pointer">Batal</button>
                </div>
            </div>

            <form method="POST" :action="isEditing ? updateUrl : '{{ route('bk.learn.store') }}'" class="flex flex-col gap-5" id="announcement-form">
                @csrf
                <template x-if="isEditing">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Judul Pengumuman -->
                <div class="flex flex-col gap-2">
                    <label class="font-label-lg text-label-lg text-on-surface flex items-center justify-between" for="post-title">
                        <span>Judul Pengumuman</span>
                        <span class="text-xs text-outline font-normal" x-text="`${title.length}/160 karakter`"></span>
                    </label>
                    <input x-model="title" name="title" required maxlength="160" class="w-full px-4 py-3 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:outline-none focus:bg-surface-container-lowest focus:shadow-md transition-all border border-transparent focus:border-secondary" id="post-title" placeholder="Misal: Pekan Konseling Ramah Remaja..." type="text">
                </div>

                <!-- Target Penerima Materi -->
                <div class="flex flex-col gap-2">
                    <label class="font-label-lg text-label-lg text-on-surface">Target Penerima Materi</label>
                    <input type="hidden" name="audience" :value="audience">
                    <div class="grid grid-cols-3 gap-2" id="audience-selector">
                        <button class="px-3 py-2.5 rounded-xl font-label-md text-label-md transition-all flex items-center justify-center gap-1.5 cursor-pointer" :class="audience === 'siswa' ? 'bg-secondary text-white font-bold shadow-xs' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high'" type="button" @click="audience = 'siswa'" :aria-pressed="audience === 'siswa'">
                            <span class="material-symbols-outlined text-base">school</span>
                            <span>Siswa</span>
                        </button>
                        <button class="px-3 py-2.5 rounded-xl font-label-md text-label-md transition-all flex items-center justify-center gap-1.5 cursor-pointer" :class="audience === 'orang_tua' ? 'bg-secondary text-white font-bold shadow-xs' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high'" type="button" @click="audience = 'orang_tua'" :aria-pressed="audience === 'orang_tua'">
                            <span class="material-symbols-outlined text-base">diversity_1</span>
                            <span>Orang Tua</span>
                        </button>
                        <button class="px-3 py-2.5 rounded-xl font-label-md text-label-md transition-all flex items-center justify-center gap-1.5 cursor-pointer" :class="audience === 'umum' ? 'bg-secondary text-white font-bold shadow-xs' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high'" type="button" @click="audience = 'umum'" :aria-pressed="audience === 'umum'">
                            <span class="material-symbols-outlined text-base">public</span>
                            <span>Umum</span>
                        </button>
                    </div>
                </div>

                <!-- Isi Pengumuman -->
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <label class="font-label-lg text-label-lg text-on-surface" for="post-content">Isi Pengumuman / Pesan Edukasi</label>
                    </div>
                    <div class="rounded-xl bg-surface-container-low overflow-hidden border border-outline-variant/20 focus-within:border-secondary transition-all">
                        <div class="flex items-center gap-1 px-3 py-1.5 bg-surface-container text-on-surface-variant text-xs">
                            <span class="font-label-sm text-label-sm font-semibold mr-2 text-outline">Format Teks:</span>
                            <span class="px-2 py-0.5 rounded bg-surface-container-lowest font-mono text-[10px]">Teks Aman</span>
                            <span class="px-2 py-0.5 rounded bg-surface-container-lowest font-mono text-[10px]">Pemberitahuan Otomatis</span>
                        </div>
                        <textarea x-model="content" name="content" required class="w-full p-4 bg-transparent text-on-surface font-body-md text-body-md placeholder:text-outline focus:outline-none resize-none leading-relaxed" id="post-content" placeholder="Ketik uraian informasi, anjuran perlindungan, tautan pelaporan resmi..." rows="5"></textarea>
                    </div>
                </div>

                <!-- Toggle Tampilkan di Portal Siswa -->
                <div class="p-4 rounded-xl bg-surface-container-low flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-tertiary-fixed text-on-tertiary-fixed flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">visibility</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-label-lg text-label-lg text-on-surface font-semibold">Tampilkan di Portal Siswa</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span x-text="visible ? 'Aktif / Tampil' : 'Disembunyikan'" :class="visible ? 'bg-tertiary-fixed text-on-tertiary-fixed' : 'bg-slate-200 text-slate-600'" class="px-2.5 py-1 rounded-full font-label-sm text-label-sm font-bold tracking-tight"></span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input x-model="visible" name="is_visible" value="1" class="sr-only peer" id="toggle-visible" type="checkbox">
                            <div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-surface-container-lowest after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface-container-lowest after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                        </label>
                    </div>
                </div>

                <!-- Preview Keamanan Klien -->
                <div class="p-3.5 rounded-xl bg-surface-container-high/40 flex flex-col gap-1.5">
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">shield</span> Preview Keamanan Klien
                    </span>
                    <p x-text="content.trim() ? content.trim() : '(Pratinjau kosong)'" class="font-body-sm text-body-sm text-on-surface line-clamp-2 italic" id="preview-text"></p>
                </div>

                <!-- Submit & Cancel Buttons -->
                <div class="flex items-center gap-3 pt-2">
                    <button class="flex-1 py-3 px-4 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-semibold shadow-md hover:bg-primary-container transition-all flex items-center justify-center gap-2 cursor-pointer" type="submit">
                        <span class="material-symbols-outlined text-lg" x-text="isEditing ? 'edit_note' : 'save'">save</span>
                        <span x-text="isEditing ? 'Perbarui Pengumuman' : 'Simpan Pengumuman'">Simpan Pengumuman</span>
                    </button>
                    <button class="px-5 py-3 rounded-xl bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high font-label-lg text-label-lg transition-all cursor-pointer" type="button" @click="resetAnnouncement()">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- RIGHT COLUMN: Arsip Materi Diterbitkan (7 cols) -->
    <div class="lg:col-span-7 flex flex-col gap-6">
        <!-- Controls & Filters -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-2">
                <span class="font-title-lg text-title-lg text-on-surface font-bold">Arsip Materi Diterbitkan</span>
                <span class="px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm font-semibold">{{ $materials->count() }} Tersedia</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Filter Audience -->
                <select x-model="filterAudience" class="px-3 py-1.5 rounded-xl bg-surface-container-lowest shadow-xs text-xs font-semibold text-on-surface-variant border border-surface-container focus:outline-none focus:border-secondary cursor-pointer">
                    <option value="all">Semua Kanal</option>
                    <option value="siswa">Siswa</option>
                    <option value="orang_tua">Orang Tua</option>
                    <option value="umum">Umum</option>
                </select>

                <!-- Filter Status -->
                <select x-model="filterStatus" class="px-3 py-1.5 rounded-xl bg-surface-container-lowest shadow-xs text-xs font-semibold text-on-surface-variant border border-surface-container focus:outline-none focus:border-secondary cursor-pointer">
                    <option value="all">Semua Status</option>
                    <option value="active">Portal Aktif</option>
                    <option value="hidden">Disembunyikan</option>
                </select>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="relative w-full">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
            <input x-model="searchQuery" type="text" placeholder="Cari materi berdasarkan judul, kode, atau isi..." class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest text-xs text-slate-900 rounded-xl border border-surface-container shadow-xs focus:outline-none focus:border-secondary transition-all">
        </div>

        <!-- Cards List -->
        <div class="flex flex-col gap-4">
            @forelse($materials as $material)
                @php
                    $audienceLabel = method_exists($material, 'audienceLabel') 
                        ? $material->audienceLabel() 
                        : ($material->audience === 'orang_tua' ? 'Orang Tua & Wali' : ($material->audience === 'umum' ? 'Umum & Satgas TPPK' : 'Siswa & Remaja'));
                    
                    $audienceBadgeClass = method_exists($material, 'audienceBadgeClass')
                        ? $material->audienceBadgeClass()
                        : ($material->audience === 'orang_tua' ? 'bg-amber-100 text-amber-800' : ($material->audience === 'umum' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800'));

                    $dateFormatted = $material->updated_at ? \Carbon\Carbon::parse($material->updated_at)->translatedFormat('d M Y') : 'Hari ini';

                    $itemData = [
                        'id'             => $material->id,
                        'code'           => $material->code,
                        'title'          => $material->title,
                        'audience'       => $material->audience,
                        'audience_label' => $audienceLabel,
                        'content'        => $material->content,
                        'is_visible'     => (bool) $material->is_visible,
                        'author_name'    => $material->author_name ?? 'Sarah Jenkins, M.Pd.',
                        'date'           => $dateFormatted,
                        'views_count'    => $material->views_count ?? 0,
                        'update_url'     => route('bk.learn.update', $material->id),
                        'toggle_url'     => route('bk.learn.toggle', $material->id),
                        'delete_url'     => route('bk.learn.destroy', $material->id),
                    ];
                @endphp

                <div x-data="{ item: @js($itemData) }" x-show="matchesFilter(item)" x-transition class="bk-hover-card rounded-2xl bg-surface-container-lowest shadow-xs p-6 flex flex-col gap-4 hover:shadow-md transition-all group bk-learning-card" :class="{ 'opacity-75 hover:opacity-100': !item.is_visible }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold {{ $audienceBadgeClass }}" x-text="item.audience_label"></span>
                                
                                <span class="px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-bold flex items-center gap-1 transition-all" :class="item.is_visible ? 'bg-tertiary-fixed text-on-tertiary-fixed' : 'bg-slate-200 text-slate-600'">
                                    <span class="w-1.5 h-1.5 rounded-full inline-block" :class="item.is_visible ? 'bg-on-tertiary-container' : 'bg-slate-400'"></span>
                                    <span x-text="item.is_visible ? 'Portal Aktif' : 'Disembunyikan'"></span>
                                </span>

                                <span class="font-label-sm text-label-sm text-outline font-mono">ID: {{ $material->code }}</span>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold group-hover:text-secondary transition-colors" x-text="item.title"></h3>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 bg-surface-container-low px-3 py-1.5 rounded-xl">
                            <span class="font-label-sm text-label-sm font-semibold" :class="item.is_visible ? 'text-on-tertiary-container' : 'text-slate-400'" x-text="item.is_visible ? 'Tampil' : 'Nonaktif'"></span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" :checked="item.is_visible" @change="toggleItemVisibility(item, $event)" class="sr-only peer" aria-label="Ubah visibilitas materi">
                                <div class="w-9 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-surface-container-lowest after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface-container-lowest after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary"></div>
                            </label>
                        </div>
                    </div>

                    <p class="font-body-md text-body-md text-on-surface-variant line-clamp-2 leading-relaxed" x-text="item.content"></p>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-surface-container-low">
                        <div class="flex items-center gap-4 text-on-surface-variant font-label-sm text-label-sm flex-wrap">
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-secondary">update</span>
                                <span>Diperbarui: {{ $dateFormatted }}</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">person</span>
                                <span>{{ $material->author_name ?? 'Sarah Jenkins, M.Pd.' }}</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">bar_chart</span>
                                <span>{{ number_format($material->views_count ?? 0) }} Tayangan</span>
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="px-3 py-1.5 rounded-xl bg-surface-container-low text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors flex items-center gap-1 cursor-pointer" type="button" @click="previewItem(item)">
                                <span class="material-symbols-outlined text-base">preview</span>
                                <span>Pratinjau</span>
                            </button>
                            <button class="px-3 py-1.5 rounded-xl bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors flex items-center gap-1 cursor-pointer" type="button" @click="editItem(item)">
                                <span class="material-symbols-outlined text-base">edit</span>
                                <span>Ubah</span>
                            </button>
                            <form method="POST" :action="item.delete_url" class="inline m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi pengumuman ini? Data yang dihapus tidak dapat dipulihkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-xl text-rose-500 hover:bg-rose-50 hover:text-rose-700 transition-colors flex items-center justify-center cursor-pointer" title="Hapus materi">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center bg-surface-container-lowest rounded-2xl border border-dashed border-slate-200 flex flex-col items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-5xl text-slate-300">auto_stories</span>
                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Pengumuman Diterbitkan</h4>
                    <p class="text-xs text-slate-500 max-w-sm">Tulis informasi pencegahan atau edukasi ramah siswa pertama Anda melalui formulir di sebelah kiri.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

</div>
</div>

<x-bk.text-preview />
</main>
</x-layouts.bk>
