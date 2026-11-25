<x-layouts.bk title="Dashboard BK" page="dashboard" mode="dashboard" :report-count="$unreadCount ?? 0">
<x-slot:toolbar>
    <nav class="staff-mode-switch" aria-label="Mode antrean laporan">
        <a href="{{ route('bk.dashboard') }}" @if (empty($isTrainingView)) aria-current="page" @endif>Data Asli</a>
        <a href="{{ route('bk.dashboard', ['mode' => 'latihan']) }}" @if (!empty($isTrainingView)) aria-current="page" @endif>Mode Latihan</a>
    </nav>
</x-slot:toolbar>
<main x-data="bkTable('dashboard')" @bk-search.window="query = $event.detail.query; statusTab = 0; selected = []; currentPage = 1" class="bk-reports-page p-8 w-full source-space-y-6">
<header class="staff-page-intro">
    <h1>Antrean Laporan</h1>
    <p>Tinjau laporan masuk dan tentukan tindak lanjut pendampingan siswa.</p>
</header>
@if (!empty($isTrainingView) || !empty($trainingSettingActive))
    <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        {{ !empty($isTrainingView) ? 'Mode Latihan (Simulasi): hanya laporan simulasi yang ditampilkan.' : 'Form Publik sedang Mode Latihan' }}
    </p>
@endif
<x-bk.report-metrics :unread="$unreadCount" :urgent="$urgentUnreadCount" :overdue="$staleCount" />
<section class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
<div class="px-6 pt-5 pb-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
<div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-lg">
<button class="tab-btn px-4 py-1.5 rounded-md text-xs transition-all flex items-center gap-1.5 text-slate-500 font-medium" type="button" @click="statusTab = 0; priorityFilter = ''; currentPage = 1; selected = []" :class="{ 'bk-tab-active': statusTab === 0 }" :aria-pressed="statusTab === 0">
<span>Belum Dibaca</span>
<span class="px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold">{{ $tabBelumDibaca }}</span>
</button>
<button class="tab-btn px-4 py-1.5 rounded-md text-xs hover:text-slate-800 transition-all flex items-center gap-1.5 text-slate-500 font-medium" type="button" @click="statusTab = 1; priorityFilter = ''; currentPage = 1; selected = []" :class="{ 'bk-tab-active': statusTab === 1 }" :aria-pressed="statusTab === 1">
<span>Sedang Ditangani</span>
<span class="px-1.5 py-0.2 rounded-full bg-slate-200 text-slate-600 text-[10px]">{{ $tabSedangDitangani }}</span>
</button>
<button class="tab-btn px-4 py-1.5 rounded-md text-xs hover:text-slate-800 transition-all flex items-center gap-1.5 text-slate-500 font-medium" type="button" @click="statusTab = 2; priorityFilter = ''; currentPage = 1; selected = []" :class="{ 'bk-tab-active': statusTab === 2 }" :aria-pressed="statusTab === 2">
<span>Selesai</span>
<span class="px-1.5 py-0.2 rounded-full bg-slate-200 text-slate-600 text-[10px]">{{ $tabSelesai }}</span>
</button>
</div>
<div class="flex items-center gap-3">
<div x-cloak x-show="selected.length > 0" x-transition.opacity class="text-xs text-slate-600 font-medium " id="selectionCounter">
<span x-text="selected.length" class="font-bold text-slate-900" id="selectedCount">1</span> laporan dipilih
            </div>
<button @click="openModal('isengModal')" :disabled="selected.length === 0" :class="{ 'bk-bulk-active': selected.length > 0 }" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all border border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed" id="bulkActionBtn" type="button">
<span class="material-symbols-outlined text-[16px]">report</span>
<span>Tandai Iseng</span>
</button>
</div>
</div>
<div class="px-6 py-3.5 bg-slate-50/70 border-b border-slate-200/80 flex flex-wrap items-center gap-2.5">
<div class="relative flex-1 min-w-[240px]">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
<input x-model="query" @input="currentPage = 1; selected = []" aria-label="Cari data tabel" class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200/60 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all" placeholder="Cari ID kasus (cth: RPT-0001) atau kata kunci..." type="text">
</div>
<x-bk.report-filters>
    <label>Jenis kejadian
        <select x-model="draft.type">
            <option value="">Jenis: Semua</option>
            @if(isset($categories) && $categories->isNotEmpty())
            @foreach($categories as $cat)
            <option value="{{ strtolower($cat->label) }}">{{ $cat->label }}</option>
            @endforeach
            @else
            <option value="fisik">Fisik</option>
            <option value="verbal">Ejekan &amp; Verbal</option>
            <option value="dikucilkan">Dikucilkan</option>
            <option value="cyber">Cyberbullying</option>
            <option value="pelecehan">Pelecehan</option>
            <option value="lainnya">Lainnya</option>
            @endif
        </select>
    </label>
    <label>Peran pelapor
        <select x-model="draft.role">
            <option value="">Peran: Semua</option>
            <option value="siswa">Korban Langsung</option>
            <option value="saksi">Saksi Kejadian</option>
            <option value="teman">Teman Korban</option>
            <option value="ortu">Orang Tua / Wali</option>
            <option value="guru">Warga Sekolah / Guru</option>
        </select>
    </label>
    <label>Urgensi
        <select x-model="draft.urgency">
            <option value="">Urgensi: Semua</option>
            <option value="urgent">Darurat / Urgent</option>
            <option value="medium">Sedang / Medium</option>
            <option value="easy">Rendah / Konseling</option>
        </select>
    </label>
    <label>Ingin dihubungi
        <select x-model="draft.contact">
            <option value="">Kontak: Semua</option>
            <option value="ya">Ingin Dihubungi (Ya)</option>
            <option value="tidak">Tidak Ingin Dihubungi</option>
        </select>
    </label>
    <label class="filter-period">Rentang waktu
        <select x-model="draft.period">
            <option value="">Waktu: Semua</option>
            <option value="7">7 Hari Terakhir</option>
            <option value="30">30 Hari Terakhir</option>
            <option value="custom">Kustom</option>
        </select>
    </label>
</x-bk.report-filters>
<x-bk.report-sort />
</div>
<div class="bk-reports-scroll overflow-x-auto" tabindex="0" role="region" aria-label="Tabel laporan, geser horizontal bila diperlukan">
<table class="bk-reports-table w-full text-left border-collapse">
<thead>
<tr class="border-b border-slate-200 bg-slate-50/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 select-none">
<th class="py-3 px-4 w-10 text-center">
<input @change="selectVisible($event.target.checked)" :checked="allSelected" :indeterminate.prop="selected.length > 0 && !allSelected" aria-label="Pilih semua laporan yang terlihat" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" id="selectAll" type="checkbox">
</th>
<th class="py-3 px-3">Kode Tiket</th>
<th class="py-3 px-3">Jenis Kejadian</th>
<th class="py-3 px-3">Peran Pelapor</th>
<th class="py-3 px-3 ">Cuplikan Cerita</th>
<th class="py-3 px-3 whitespace-nowrap">Waktu</th>
<th class="py-3 px-3">Urgensi</th>
<th class="py-3 px-4 text-right">Aksi</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 text-xs text-slate-700">
@forelse($reports as $index => $report)
@php
    $urgencyClass = match(strtolower($report->urgency ?: '')) {
        'darurat', 'urgent' => 'bg-rose-100 text-rose-700 border-rose-200',
        'sedang', 'medium', 'tinggi', 'penting' => 'bg-amber-100 text-amber-800 border-amber-200',
        default => 'bg-emerald-50 text-emerald-700 border-emerald-200'
    };
    $urgencyIcon = match(strtolower($report->urgency ?: '')) {
        'darurat', 'urgent' => 'error',
        'sedang', 'medium', 'tinggi', 'penting' => 'warning',
        default => 'check_circle'
    };
    $urgencyText = match(strtolower($report->urgency ?: '')) {
        'darurat', 'urgent' => 'Darurat',
        'sedang', 'medium', 'tinggi', 'penting' => 'Sedang',
        default => 'Rendah'
    };
    $roleIcon = match(strtolower($report->reporter_role ?: '')) {
        'guru' => 'school',
        'orang tua', 'orang tua-wali', 'ortu' => 'family_restroom',
        'teman korban', 'saksi' => 'group',
        default => 'person'
    };
    $isUnread = in_array($report->status, ['belum_dibaca', 'unread']);
    $isStale = !in_array($report->status, ['selesai', 'resolved', 'iseng'])
        && $report->status_updated_date
        && $report->status_updated_date->lte(now()->subDays(3));
    $statusType = $isUnread ? 'unread' : (in_array($report->status, ['selesai', 'resolved']) ? 'resolved' : 'in_progress');
@endphp
<tr class="hover:bg-slate-50 transition-colors {{ $isUnread && $report->urgency_normalized === 'urgent' ? 'bg-rose-50/20 hover:bg-rose-50/40' : ($isUnread && $report->urgency_normalized === 'medium' ? 'bg-amber-50/15 hover:bg-amber-50/30' : '') }}"
    data-preview-row="{{ $index }}"
    data-id="{{ $report->id }}"
    data-status="{{ $statusType }}"
    data-urgency="{{ $report->urgency_normalized }}"
    data-category="{{ $report->category_normalized }}"
    data-category-label="{{ strtolower($report->category) }}"
    data-role="{{ $report->role_normalized }}"
    data-contact="{{ $report->wants_contact ? 'ya' : 'tidak' }}"
    data-stale="{{ $isStale ? '1' : '0' }}"
    data-date="{{ $report->submitted_date ? $report->submitted_date->format('Y-m-d') : '' }}"
    x-show="rowVisible({{ $index }})">
<td class="py-3 px-4 text-center">
<input x-model="selected" value="{{ $report->id }}" aria-label="Pilih laporan" class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" type="checkbox">
</td>
<td class="py-3 px-3">
<div class="flex items-center gap-2 font-bold text-slate-900 tracking-tight whitespace-nowrap">
@if($isUnread)
<span class="w-2 h-2 rounded-full bg-blue-600 ring-2 ring-blue-100" title="Belum dibaca"></span>
@elseif($statusType === 'in_progress')
<span class="w-2 h-2 rounded-full bg-amber-500 ring-2 ring-amber-100" title="Sedang ditangani"></span>
@else
<span class="w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-emerald-100" title="Selesai"></span>
@endif
<span>#{{ $report->case_label }}</span>
</div>
</td>
<td class="py-3 px-3 font-medium text-slate-800">
<div>{{ $report->category }}</div>
@if($report->ml_status === 'selesai')
<span class="mt-0.5 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $report->ml_label === 1 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600' }}">{{ $report->ml_badge_label }}</span>
@endif
</td>
<td class="py-3 px-3 text-slate-600">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-[11px]">
<span class="material-symbols-outlined text-[13px] text-slate-500">{{ $roleIcon }}</span>
{{ $report->reporter_role ?: 'Siswa' }}
</span>
<small class="report-cell-meta">Ingin dihubungi: {{ $report->wants_contact ? 'Ya (WhatsApp)' : 'Tidak' }}</small>
</td>
<td class="py-3 px-3 text-slate-600">
<p class="bk-report-snippet line-clamp-1 max-w-[320px]" title="{{ $report->story }}">
{{ Str::limit($report->story, 85) }}
</p>
</td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap font-medium">
{{ $report->formatted_time }}
<small class="report-cell-meta">{{ $report->assigned_counselor ? 'Ditangani '.$report->assigned_counselor : 'Belum ditugaskan' }}</small>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold border {{ $urgencyClass }}">
<span class="material-symbols-outlined text-[15px] fill-1 {{ str_contains($urgencyClass, 'rose') ? 'text-rose-600' : (str_contains($urgencyClass, 'amber') ? 'text-amber-600' : 'text-emerald-600') }}">{{ $urgencyIcon }}</span>
<span>{{ $urgencyText }}</span>
</span>
</td>
<td class="py-3 px-4 text-right whitespace-nowrap">
<a aria-label="Buka laporan {{ $report->case_label }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-all shadow-2xs" href="{{ route('bk.reports.show', $report->id) }}">
<span>Buka</span>
<span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
@empty
@endforelse
<tr x-cloak x-show="filteredCount === 0">
<td colspan="8" class="p-8 text-center text-sm text-slate-500">Tidak ada laporan yang cocok dengan pilihan filter ini.</td>
</tr>
</tbody>
</table>
</div>
<div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500">
<div class="flex items-center gap-3">
<div class="flex items-center gap-2">
<span class="inline-block w-2 h-2 rounded-full bg-blue-600">
</span>
<span class="font-medium text-slate-600">
<span x-text="'Menampilkan ' + pageStart + ' - ' + pageEnd + ' dari ' + filteredCount + ' laporan'">
</span>
</span>
</div>
</div>
<div class="flex items-center gap-1.5 select-none">
<div class="flex items-center gap-3 select-none">
<div class="flex items-center gap-1.5">
<span class="text-slate-400 font-medium">Tampilkan:</span>
<div class="relative inline-flex items-center rounded-lg border border-slate-200 bg-white hover:border-slate-300 hover:shadow-xs transition-all group">
<select class="appearance-none bg-transparent hover:bg-slate-50/50 text-slate-700 text-xs font-semibold rounded-lg pl-3 pr-7 py-1.5 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all border-0" style="border: none; outline: none; box-shadow: none;" @change="pageSizeChanged($event)">
<option value="6">6 / halaman</option>
<option value="10" selected>10 / halaman</option>
<option value="25">25 / halaman</option>
</select>
<span class="material-symbols-outlined absolute right-1.5 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">expand_more</span>
</div>
</div>
<div class="flex items-center gap-1">
<button type="button" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center transition-all" :class="currentPage <= 1 ? 'text-slate-300 cursor-not-allowed bg-slate-50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 cursor-pointer'" :disabled="currentPage <= 1" @click="prevPage()">
<span class="material-symbols-outlined text-[16px]">chevron_left</span>
</button>
<span class="px-2 text-xs font-semibold text-slate-700" x-text="currentPage + ' / ' + totalPages"></span>
<button type="button" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center transition-all" :class="currentPage >= totalPages ? 'text-slate-300 cursor-not-allowed bg-slate-50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 cursor-pointer'" :disabled="currentPage >= totalPages" @click="nextPage()">
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
</button>
</div>
</div>
</div>
</div>
</section>
<template x-teleport="body">
<div role="dialog" aria-modal="true" tabindex="-1" @keydown.escape.window="closeModal()" @keydown.tab="trapFocus($event)" x-cloak x-show="modal === 'isengModal'" x-transition.opacity class="bk-modal bk-dashboard-theme fixed inset-0 z-50 " id="isengModal">
<div @click="closeModal()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" id="modalBackdrop">
</div>
<div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4">
<div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 source-space-y-4">
<div class="flex items-start gap-4">
<div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[24px]">warning</span>
</div>
<div class="source-space-y-1">
<h3 class="text-base font-bold text-slate-900">Tandai sebagai Laporan Iseng?</h3>
<p class="text-xs text-slate-600 leading-relaxed">
              Laporan yang ditandai iseng akan dipindahkan dari antrean aktif ke arsip tanpa notifikasi publik. Tindakan ini tercatat dalam log audit.
            </p>
</div>
</div>
<div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-100">
<button @click="closeModal()" class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors" id="cancelModalBtn" type="button">
            Batal
          </button>
<button @click="submitBulkIseng()" class="px-4 py-2 rounded-lg text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white transition-colors shadow-xs" id="confirmModalBtn" type="button">
            Ya, Tandai Iseng
          </button>
</div>
</div>
</div>
</div>
</template>
</main>
</x-layouts.bk>
