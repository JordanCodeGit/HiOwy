<x-layouts.bk title="Log Akses" page="access-log" mode="dashboard" :report-count="$unreadCount ?? 0">
<main x-data="bkTable('access-log')" class="relative w-full min-h-screen bg-background">
<div class="flex flex-col w-full">
<div class="p-8 max-w-[1440px] mx-auto w-full source-space-y-6">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
<div>
<div class="flex items-center gap-2 mb-1.5">
<span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
<span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary font-bold">Kepatuhan Regulasi &amp; Privasi Siswa</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight font-bold">Log Akses &amp; Audit Keamanan</h1>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Catatan audit bersifat append-only (hanya tambah, tidak dapat diubah atau dihapus).</p>
</div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<div class="bk-hover-card bg-surface-container-lowest p-5 rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Entri Tercatat</span>
<span class="material-symbols-outlined text-secondary text-xl">receipt_long</span>
</div>
<div class="mt-3">
<span class="font-headline-md text-headline-md font-bold text-on-surface">{{ $totalLogs }}</span>
<div class="flex items-center gap-1.5 text-on-tertiary-container mt-0.5">
<span class="material-symbols-outlined text-sm">security_update_good</span>
<span class="font-body-sm text-body-sm font-medium">Append-Only Permanen</span>
</div>
</div>
</div>
<div class="bk-hover-card bg-surface-container-lowest p-5 rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Akses &amp; Aksi Kasus</span>
<span class="material-symbols-outlined text-secondary text-xl">visibility</span>
</div>
<div class="mt-3">
<span class="font-headline-md text-headline-md font-bold text-on-surface">{{ $sensitiveViewsCount }}</span>
<div class="flex items-center gap-1.5 text-on-surface-variant mt-0.5">
<span class="material-symbols-outlined text-sm">badge</span>
<span class="font-body-sm text-body-sm">Tindakan Staf Terverifikasi</span>
</div>
</div>
</div>
<div class="bk-hover-card bg-surface-container-lowest p-5 rounded-xl shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Otorisasi / Login Gagal</span>
<span class="material-symbols-outlined text-on-surface-variant text-xl">shield_person</span>
</div>
<div class="mt-3">
<span class="font-headline-md text-headline-md font-bold text-on-surface">{{ $failedAuthCount }}</span>
<div class="flex items-center gap-1.5 text-on-tertiary-container mt-0.5">
<span class="material-symbols-outlined text-sm">verified_user</span>
<span class="font-body-sm text-body-sm font-medium">{{ $failedAuthCount === 0 ? 'Tidak Ada Anomali' : 'Terdeteksi Percobaan Gagal' }}</span>
</div>
</div>
</div>
</div>

<div class="bg-surface-container-lowest p-4 rounded-xl shadow-xs flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
<div class="relative flex-1 max-w-md">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
<input x-model="query" @input="selected = []" aria-label="Cari data log akses" class="w-full pl-9 pr-4 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 transition-all" id="auditSearch" placeholder="Cari nama staf, IP, aksi, atau ID kasus..." type="text">
</div>
<div class="relative min-w-[180px]">
<select class="w-full appearance-none bg-surface-container-low py-2 pl-3.5 pr-8 rounded-lg font-body-sm text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all cursor-pointer" id="roleFilter" @change="filterChanged($event)">
<option value="all">Semua Peran</option>
<option value="bk">Guru BK</option>
<option value="kesiswaan">Tim Kesiswaan</option>
<option value="walas">Wali Kelas</option>
<option value="system">Keamanan Sistem</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">expand_more</span>
</div>
</div>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-xs overflow-hidden flex flex-col">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse" id="auditTable">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
<th class="py-3.5 px-6 font-semibold w-[200px]" scope="col">Waktu &amp; Tanggal</th>
<th class="py-3.5 px-6 font-semibold w-[260px]" scope="col">Pengguna &amp; Peran</th>
<th class="py-3.5 px-6 font-semibold min-w-[240px]" scope="col">Aksi Sistem</th>
<th class="py-3.5 px-6 font-semibold w-[170px]" scope="col">Kasus Terkait</th>
<th class="py-3.5 px-6 font-semibold w-[240px]" scope="col">Alamat IP &amp; Perangkat</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container-low font-body-md text-body-md text-on-surface" id="tableBody">
@foreach($logs as $log)
<tr class="hover:bg-surface-container-low/50 transition-colors" data-preview-row="{{ $loop->index }}" x-show="rowVisible({{ $loop->index }})">
<td class="py-4 px-6 align-middle">
<div class="flex flex-col">
<span class="font-headline-sm text-headline-sm text-xs font-mono font-semibold tracking-tight text-on-surface">{{ $log->created_at ? $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y') : '-' }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant font-mono">{{ $log->created_at ? $log->created_at->timezone('Asia/Jakarta')->format('H:i:s') . ' WIB' : '-' }}</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg font-bold text-on-surface truncate">{{ $log->user_name }}</span>
<span class="font-label-sm text-label-sm text-secondary">{{ $log->role_label }}</span>
</div>
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col gap-1">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-sm text-label-sm w-fit">
<span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
{{ $log->action_label }}
</span>
@if($log->details)
<span class="text-[11px] text-on-surface-variant">{{ $log->details }}</span>
@endif
</div>
</td>
<td class="py-4 px-6 align-middle">
@if($log->report)
<a href="{{ route('bk.reports.show', $log->report->id) }}" class="inline-flex items-center gap-1 bg-surface-container-low px-2.5 py-1 rounded-md hover:bg-blue-50">
<span class="font-headline-sm text-headline-sm text-xs font-mono font-bold text-secondary">#{{ $log->report->case_label }}</span>
</a>
@else
<span class="text-xs text-slate-400">-</span>
@endif
</td>
<td class="py-4 px-6 align-middle">
<div class="flex flex-col text-xs text-on-surface-variant">
<span class="font-mono">{{ $log->ip_address ?: '-' }}</span>
<span class="truncate max-w-[220px]" title="{{ $log->user_agent }}">{{ $log->user_agent ?: '-' }}</span>
</div>
</td>
</tr>
@endforeach
<tr x-cloak x-show="visibleCount === 0">
<td colspan="5" class="p-8 text-center text-sm text-slate-500">
{{ $logs->isEmpty() ? 'Belum ada catatan log akses.' : 'Tidak ada entri log yang sesuai dengan pencarian ini.' }}
</td>
</tr>
</tbody>
</table>
</div>
<div class="px-6 py-4 bg-surface-container-lowest flex items-center justify-between gap-4 border-t border-surface-container-low">
<div class="font-body-sm text-body-sm text-on-surface-variant">
Menampilkan <span x-text="visibleCount">{{ $logs->count() }}</span> dari {{ $totalLogs }} entri audit (maksimal 200 terbaru)
</div>
</div>
</div>
</div>
</div>
</main>
</x-layouts.bk>
