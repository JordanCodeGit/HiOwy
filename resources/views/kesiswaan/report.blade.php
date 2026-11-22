<x-layouts.bk title="Detail Laporan Kesiswaan" page="report" mode="dashboard" portal="kesiswaan" :report-count="$summary['unread']">
@php($statuses = ['belum_dibaca' => 'Belum Dibaca', 'dibaca' => 'Dibaca', 'ditindaklanjuti' => 'Ditindaklanjuti', 'selesai' => 'Selesai', 'iseng' => 'Iseng'])
<main x-data="kesiswaanReport" class="w-full max-w-[1580px] mx-auto p-4 lg:p-8 space-y-6">
    <nav class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500"><a href="{{ route($report['archived'] ? 'kesiswaan.archive' : 'kesiswaan.dashboard') }}" class="inline-flex items-center gap-2 hover:text-secondary"><span class="material-symbols-outlined text-base">arrow_back</span>{{ $report['archived'] ? 'Arsip Laporan' : 'Semua Laporan' }}</a><span>Diterima: {{ $report['received'] }}</span></nav>
    <header class="bk-hover-card flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
        <div><div class="flex flex-wrap items-center gap-3"><h1 class="text-xl font-bold">#{{ $report['ticket'] }}</h1><x-kesiswaan.urgency :value="$report['urgency']" /><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-secondary">{{ $statuses[$report['status']] }}</span>@if($report['archived'])<span class="rounded-full bg-slate-100 px-3 py-1 text-xs">Diarsipkan</span>@endif</div><p class="mt-2 text-xs text-slate-500">{{ $report['type_label'] }} · {{ $report['role'] }}</p></div>
        @if($report['archived'])
        <button type="button" @click="openModal('unarchive-report')" class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100 cursor-pointer"><span class="material-symbols-outlined text-lg">unarchive</span>Keluarkan dari Arsip</button>
        @else
        <button type="button" @click="openModal('archive-report')" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold hover:bg-slate-50 cursor-pointer"><span class="material-symbols-outlined text-lg">archive</span>Arsipkan</button>
        @endif
    </header>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <div class="lg:col-span-7 space-y-6">
            <section class="bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <h2 class="border-b border-slate-100 pb-4 text-sm font-bold uppercase tracking-wide flex items-center gap-2"><span class="material-symbols-outlined text-secondary">description</span>Informasi Insiden</h2>
                <dl class="mt-5 grid sm:grid-cols-2 gap-3">
                    @foreach(['Jenis Kejadian' => $report['type_label'], 'Tanggal Kejadian' => $report['incident_date'] ?? 'Tidak ingat pasti', 'Waktu Kejadian' => $report['incident_time'] ?? '-', 'Lokasi' => $report['location'], 'Pihak Terlibat' => $report['parties']] as $label => $value)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4"><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $label }}</dt><dd class="mt-1 text-sm font-semibold">{{ $value }}</dd></div>
                    @endforeach
                </dl>
                <h3 class="mt-6 mb-3 text-xs font-bold uppercase text-slate-500">Cerita Kejadian</h3><p class="whitespace-pre-line rounded-xl bg-slate-50 p-5 text-sm leading-7 text-slate-700">{{ $report['story'] }}</p>
            </section>
            <section class="bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <h2 class="text-sm font-bold uppercase tracking-wide flex items-center gap-2"><span class="material-symbols-outlined text-secondary">shield</span>Batasan Akses Kesiswaan</h2>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                    Kesiswaan berhak memantau substansi kejadian sesuai kategori yang diizinkan dan memperbarui status tindak lanjut disiplin sekolah. Identitas pelapor, komunikasi pelapor↔BK, catatan privat konselor, serta klasifikasi ML internal tidak ditampilkan pada portal ini.
                </p>
            </section>
        </div>
        <div class="lg:col-span-5 space-y-6">
            <section class="bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <h2 class="mb-5 text-sm font-bold uppercase tracking-wide">Status &amp; Penanganan</h2><p class="text-xs text-slate-500">Ditangani oleh</p><p class="mt-1 mb-5 text-sm font-semibold">{{ $report['assigned'] }}</p>
                @if(session('status'))
                <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-3.5 py-2.5 text-xs font-medium text-emerald-800">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('kesiswaan.reports.status', $report['id']) }}" class="space-y-3">@csrf
                    <label for="case-status" class="block text-xs font-semibold">Status Kasus</label><select id="case-status" name="status" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm">@foreach($statuses as $value => $label)@continue($value === 'iseng')<option value="{{ $value }}" @selected($report['status'] === $value)>{{ $label }}</option>@endforeach @if($report['status'] === 'iseng')<option value="iseng" selected>Iseng</option>@endif</select><button type="submit" class="w-full rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-on-primary hover:bg-primary-container">Simpan Status</button>
                </form>
                <button type="button" @click="openModal('mark-iseng')" class="mt-4 w-full rounded-xl border border-rose-200 px-4 py-2.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">Tandai Laporan Iseng</button>
            </section>
            <section class="bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold uppercase tracking-wide flex items-center gap-2"><span class="material-symbols-outlined text-secondary">edit_note</span>Catatan Tindak Lanjut Kesiswaan</h2>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Terpisah &amp; Terenkripsi</span>
                </div>
                @if(!empty($report['kesiswaan_notes']))
                <div class="rounded-xl bg-slate-50 border border-slate-100 p-3.5 space-y-2 max-h-48 overflow-y-auto">
                    @foreach(explode("\n\n", $report['kesiswaan_notes']) as $noteItem)
                    @if(trim($noteItem))
                    <div class="text-xs text-slate-700 leading-relaxed border-b border-slate-200/60 pb-1.5 last:border-b-0 last:pb-0">{{ $noteItem }}</div>
                    @endif
                    @endforeach
                </div>
                @endif
                <form method="POST" action="{{ route('kesiswaan.reports.note', $report['id']) }}" class="space-y-2.5">
                    @csrf
                    <label for="kesiswaan-note" class="block text-xs font-semibold text-slate-700">Tambah Catatan Penanganan Disiplin</label>
                    <textarea id="kesiswaan-note" name="note" rows="3" maxlength="2000" required placeholder="Catat langkah penanganan atau koordinasi non-konseling (tidak terlihat oleh pelapor)..." class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-secondary"></textarea>
                    <button type="submit" class="w-full rounded-xl bg-slate-900 hover:bg-slate-800 px-4 py-2.5 text-xs font-semibold text-white transition-colors">Simpan Catatan Kesiswaan</button>
                </form>
            </section>
            <section class="bk-hover-card rounded-2xl border border-slate-200 bg-white p-6 shadow-xs"><h2 class="mb-5 text-sm font-bold uppercase tracking-wide flex items-center gap-2"><span class="material-symbols-outlined text-secondary">history</span>Riwayat Aktivitas</h2><ol class="space-y-5 border-l border-slate-200 pl-5">@foreach($report['activities'] as $activity)<li><p class="text-sm font-semibold">{{ $activity['label'] }}</p><time class="text-xs text-slate-500">{{ $activity['time'] }}</time></li>@endforeach</ol></section>
        </div>
    </div>
    <x-kesiswaan.confirmation id="mark-iseng" title="Tandai laporan sebagai iseng?" action="Penandaan laporan iseng" :post-url="route('kesiswaan.reports.iseng', $report['id'])">Konfirmasi penandaan laporan #{{ $report['ticket'] }} sebagai laporan iseng.</x-kesiswaan.confirmation>
    @if($report['archived'])
    <x-kesiswaan.confirmation id="unarchive-report" title="Keluarkan laporan dari arsip?" action="Pemulihan laporan dari arsip" :post-url="route('kesiswaan.reports.unarchive', $report['id'])">Laporan #{{ $report['ticket'] }} akan dikembalikan ke antrean aktif Kesiswaan dan BK.</x-kesiswaan.confirmation>
    @else
    <x-kesiswaan.confirmation id="archive-report" title="Arsipkan laporan ini?" action="Pengarsipan laporan" :post-url="route('kesiswaan.reports.archive', $report['id'])">Laporan #{{ $report['ticket'] }} akan dipindahkan ke arsip setelah tindakan terintegrasi.</x-kesiswaan.confirmation>
    @endif
</main>
</x-layouts.bk>
