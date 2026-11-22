<x-layouts.bk title="Arsip Laporan" page="archive" mode="dashboard" :report-count="$unreadCount ?? 0">
<main x-data="bkTable('archive')" class="relative w-full min-h-screen bg-background">
<div class="flex flex-col w-full">
<div class="px-8 py-8 flex flex-col gap-6 max-w-7xl mx-auto w-full">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
<div class="flex flex-col gap-1.5">
<div class="inline-flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
<span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
Modul Konseling &amp; Investigasi
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight font-bold">Arsip Laporan</h1>
<p class="font-body-md text-body-md text-on-surface-variant">
Laporan terselesaikan dan ditandai iseng. Riwayat catatan dan audit trail tetap terjaga untuk kepatuhan dokumentasi.
</p>
</div>
<div class="flex items-center gap-3 self-start md:self-auto">
<div class="bg-surface-container-lowest px-4 py-2.5 rounded-xl shadow-xs flex items-center gap-3">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">folder_managed</span>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Total Terarsip</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold leading-none mt-0.5">{{ $totalArchived }} Kasus</span>
</div>
</div>
<div class="bg-surface-container-lowest px-4 py-2.5 rounded-xl shadow-xs flex items-center gap-3">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-on-tertiary-container">
<span class="material-symbols-outlined text-xl">verified</span>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Selesai Ditangani</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold leading-none mt-0.5">{{ $resolvedCount }} Kasus</span>
</div>
</div>
<div class="bg-surface-container-lowest px-4 py-2.5 rounded-xl shadow-xs flex items-center gap-3">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-slate-600">
<span class="material-symbols-outlined text-xl">flag</span>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Ditandai Iseng</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold leading-none mt-0.5">{{ $isengCount }} Kasus</span>
</div>
</div>
</div>
</div>

<div class="bg-surface-container-lowest p-4 rounded-xl shadow-xs flex flex-col xl:flex-row items-stretch xl:items-center justify-between gap-3">
<div class="relative flex-1 min-w-[240px]">
<span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
<input x-model="query" @input="selected = []" aria-label="Cari data arsip" class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low rounded-xl font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 transition-all" id="ticketSearchInput" placeholder="Cari ID kasus (RPT-XXXX) atau kategori..." type="text">
</div>
<div class="flex flex-wrap items-center gap-2.5">
<div class="relative min-w-[160px] flex-1 sm:flex-initial">
<select class="w-full appearance-none pl-3 pr-8 py-2.5 bg-surface-container-low rounded-xl font-body-sm text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all cursor-pointer" id="filterReason" @change="filterChanged($event)">
<option value="all">Alasan: Semua</option>
<option value="resolved">Selesai Ditangani</option>
<option value="spam">Ditandai Iseng</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-outline text-base pointer-events-none">expand_more</span>
</div>
<div class="relative min-w-[170px] flex-1 sm:flex-initial">
<select class="w-full appearance-none pl-3 pr-8 py-2.5 bg-surface-container-low rounded-xl font-body-sm text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all cursor-pointer" id="filterType" @change="filterChanged($event)">
<option value="all">Jenis Kejadian: Semua</option>
@foreach($archivedReports->pluck('category')->filter()->unique() as $cat)
<option value="{{ $cat }}">{{ $cat }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-outline text-base pointer-events-none">expand_more</span>
</div>
<button type="button" @click="resetFilters()" class="px-3.5 py-2.5 rounded-xl border border-outline-variant/40 text-xs font-semibold text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer inline-flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">restart_alt</span>
<span>Reset Filter</span>
</button>
<button x-cloak x-show="selected.length > 0" type="button" @click="submitBulkUnarchive()" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors cursor-pointer inline-flex items-center gap-1.5 shadow-xs">
<span class="material-symbols-outlined text-base">unarchive</span>
<span>Keluarkan dari Arsip (<span x-text="selected.length"></span>)</span>
</button>
</div>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-xs overflow-hidden flex flex-col">
<div class="overflow-x-auto">
<table class="w-full text-left whitespace-nowrap">
<thead>
<tr class="bg-surface-container-low/70 text-on-surface-variant font-label-md text-label-md select-none">
<th class="py-3.5 pl-6 pr-3 w-10" scope="col">
<input @change="selectVisible($event.target.checked)" :checked="allSelected" :indeterminate.prop="selected.length > 0 && !allSelected" aria-label="Pilih semua laporan arsip" class="w-4 h-4 rounded text-secondary bg-surface-container-lowest focus:ring-secondary/20 transition cursor-pointer" id="selectAllCheckbox" type="checkbox">
</th>
<th class="py-3.5 px-4 font-semibold" scope="col">ID Kasus</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Jenis Kejadian</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Peran Pelapor</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Alasan Arsip</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Tanggal Masuk</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Tanggal Diarsipkan</th>
<th class="py-3.5 pl-4 pr-6 text-right font-semibold" scope="col">Aksi</th>
</tr>
</thead>
<tbody class="font-body-md text-body-md text-on-surface" id="archiveTableBody">
@foreach($archivedReports as $report)
<tr class="hover:bg-surface-container-low/40 transition-colors group" data-preview-row="{{ $loop->index }}" data-id="{{ $report->id }}" x-show="rowVisible({{ $loop->index }})">
<td class="py-4 pl-6 pr-3">
<input x-model="selected" value="{{ $report->id }}" aria-label="Pilih laporan #{{ $report->case_label }}" class="row-checkbox w-4 h-4 rounded text-secondary bg-surface-container-lowest focus:ring-secondary/20 transition cursor-pointer" type="checkbox">
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-body-md font-bold tracking-tight text-on-surface">#{{ $report->case_label }}</span>
</div>
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2 font-medium">
<span class="w-2 h-2 rounded-full {{ $report->archive_reason === 'iseng' ? 'bg-outline' : 'bg-secondary' }}"></span>
{{ $report->category }}
</div>
</td>
<td class="py-4 px-4 text-on-surface-variant font-medium">
{{ $report->reporter_role ?: 'Siswa' }}
</td>
<td class="py-4 px-4">
@if($report->archive_reason === 'iseng')
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm bg-surface-container-high text-on-surface-variant font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-outline"></span>
Ditandai Iseng
</span>
@else
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm bg-surface-container text-on-tertiary-container font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container"></span>
Selesai Ditangani
</span>
@endif
</td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
{{ $report->formatted_time }}
</td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
{{ $report->archived_at ? \Carbon\Carbon::parse($report->archived_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y') : '-' }}
</td>
<td class="py-4 pl-4 pr-6 text-right">
<div class="inline-flex items-center gap-2 justify-end">
<button type="button" @click="unarchiveSingle({{ $report->id }})" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white rounded-lg font-label-sm text-label-sm font-semibold transition-all cursor-pointer" title="Keluarkan laporan dari arsip">
<span class="material-symbols-outlined text-sm">unarchive</span>
<span>Pulihkan</span>
</button>
<a class="inline-flex items-center gap-1 px-3 py-1.5 bg-surface-container-low hover:bg-primary hover:text-on-primary rounded-lg font-label-sm text-label-sm text-on-surface transition-all" href="{{ route('bk.reports.show', $report->id) }}">
Buka Laporan
<span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</div>
</td>
</tr>
@endforeach
<tr x-cloak x-show="visibleCount === 0">
<td colspan="8" class="p-8 text-center text-sm text-slate-500">
{{ $archivedReports->isEmpty() ? 'Belum ada laporan yang diarsipkan.' : 'Tidak ada data arsip yang cocok dengan filter ini.' }}
</td>
</tr>
</tbody>
</table>
</div>
<div class="px-6 py-4 bg-surface-container-lowest flex items-center justify-between gap-4 border-t border-surface-container-low">
<div class="font-body-sm text-body-sm text-on-surface-variant">
Menampilkan <span x-text="visibleCount">{{ $totalArchived }}</span> dari {{ $totalArchived }} kasus terarsip
</div>
</div>
</div>
</div>
</div>
</main>
</x-layouts.bk>
