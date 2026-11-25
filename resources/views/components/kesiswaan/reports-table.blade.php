@props(['reports', 'types', 'summary', 'archive' => false])
<section class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
    @unless($archive)
    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap gap-1.5 p-1 bg-slate-100 rounded-lg">
            @foreach(['belum_dibaca' => ['Belum Dibaca', $summary['unread']], 'handling' => ['Sedang Ditangani', $summary['handling']], 'selesai' => ['Selesai', $summary['completed']]] as $key => [$label, $count])
            <button type="button" @click="status = '{{ $key }}'; selected = []" :class="{ 'bk-tab-active': status === '{{ $key }}' }" :aria-pressed="status === '{{ $key }}'" class="rounded-md px-4 py-2 text-xs font-medium text-slate-500">{{ $label }} <span class="ml-2 rounded-full bg-slate-200 px-1.5 text-[10px]">{{ $count }}</span></button>
            @endforeach
        </div>
        <button type="button" @click="openModal('bulk-iseng')" :disabled="!selected.length" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-rose-50 disabled:opacity-40 disabled:cursor-not-allowed"><span class="material-symbols-outlined text-base">report</span>Tandai Iseng <span x-show="selected.length" x-text="'(' + selected.length + ')'"></span></button>
    </div>
    @endunless
    <div class="flex flex-wrap gap-3 border-b border-slate-200 bg-slate-50/70 px-6 py-4">
        <label class="relative flex-1 min-w-56"><span class="sr-only">Cari laporan</span><span class="material-symbols-outlined absolute left-3 top-2 text-slate-400 text-lg">search</span><input x-model="query" @input="selected = []" type="search" placeholder="Cari kode tiket atau jenis kejadian..." class="w-full rounded-lg border-slate-200 bg-white pl-10 py-2 text-sm"></label>
        @if ($archive)
        <label><span class="sr-only">Jenis kejadian</span><select x-model="filters.type" @change="selected = []" class="rounded-lg border-slate-200 bg-white py-2 text-sm"><option value="">Jenis: Semua</option>@foreach($types as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></label>
        <label><span class="sr-only">Alasan arsip</span><select x-model="filters.reason" class="rounded-lg border-slate-200 bg-white py-2 text-sm"><option value="">Alasan: Semua</option>@foreach($reports->pluck('reason')->unique() as $reason)<option>{{ $reason }}</option>@endforeach</select></label>
        <label><span class="sr-only">Periode</span><select x-model="period" @change="selected = []" class="rounded-lg border-slate-200 bg-white py-2 text-sm"><option value="">Waktu: Semua</option><option value="{{ now('Asia/Jakarta')->toDateString() }}">Hari Ini</option><option value="{{ now('Asia/Jakarta')->subDays(7)->toDateString() }}">7 Hari Terakhir</option><option value="custom">Rentang tanggal</option></select></label>
        <div x-cloak x-show="period === 'custom'" class="flex flex-wrap gap-2"><label class="text-xs">Dari <input type="date" x-model="dateFrom" @change="selected = []" class="rounded-lg border-slate-200 text-sm"></label><label class="text-xs">Sampai <input type="date" x-model="dateTo" @change="selected = []" class="rounded-lg border-slate-200 text-sm"></label></div>
        <button type="button" @click="resetFilters()" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 cursor-pointer"><span class="material-symbols-outlined text-base">restart_alt</span>Reset Filter</button>
        @else
            <x-bk.report-filters>
                <label>Jenis kejadian
                    <select x-model="draft.type">
                        <option value="">Jenis: Semua</option>@foreach($types as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                    </select>
                </label>
                <label>Peran pelapor
                    <select x-model="draft.role">
                        <option value="">Peran: Semua</option>@foreach($reports->pluck('role')->unique() as $role)<option>{{ $role }}</option>@endforeach
                    </select>
                </label>
                <label>Urgensi
                    <select x-model="draft.urgency">
                        <option value="">Urgensi: Semua</option><option value="Darurat">Darurat</option><option value="Sedang">Sedang</option><option value="Rendah">Rendah</option>
                    </select>
                </label>
                <label>Ingin dihubungi
                    <select x-model="draft.contact">
                        <option value="">Ingin dihubungi: Semua</option><option value="ya">Ya</option><option value="tidak">Tidak</option>
                    </select>
                </label>
                <label class="filter-period">Rentang waktu
                    <select x-model="draft.period">
                        <option value="">Waktu: Semua</option><option value="{{ now('Asia/Jakarta')->toDateString() }}">Hari Ini</option><option value="{{ now('Asia/Jakarta')->subDays(7)->toDateString() }}">7 Hari Terakhir</option><option value="custom">Rentang tanggal</option>
                    </select>
                </label>
            </x-bk.report-filters>
            <x-bk.report-sort default-order="Terbaru" />
        @endif
    </div>
    <div class="bk-reports-scroll overflow-x-auto">
        <table class="bk-reports-table w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wide"><tr>
                @unless($archive)<th class="p-4"><input type="checkbox" aria-label="Pilih semua laporan yang terlihat" :checked="allSelected" @change="selectVisible($event.target.checked)"></th>@endunless
                @foreach($archive ? ['Kode Tiket', 'Jenis Kejadian', 'Peran Pelapor', 'Alasan Arsip', 'Waktu Masuk', 'Diarsipkan', 'Aksi'] : ['Kode Tiket', 'Jenis Kejadian', 'Peran Pelapor', 'Cuplikan Cerita', 'Waktu', 'Urgensi', 'Aksi'] as $heading)<th scope="col" class="px-4 py-3 whitespace-nowrap font-bold">{{ $heading }}</th>@endforeach
            </tr></thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
            @foreach($reports as $report)
                <tr data-report-row data-danger="{{ $report['danger_flag'] ? 'true' : 'false' }}" data-id="{{ $report['id'] }}" data-type="{{ $report['type'] }}" data-role="{{ $report['role'] }}" data-urgency="{{ $report['urgency'] }}" data-contact="{{ $report['wants_contact'] ? 'ya' : 'tidak' }}" data-status="{{ $report['status'] }}" data-reason="{{ $report['reason'] }}" data-date="{{ $report['date'] }}" x-show="rowVisible({{ $loop->index }})" class="hover:bg-blue-50/40 transition-colors">
                    @unless($archive)<td class="p-4"><input type="checkbox" x-model="selected" value="{{ $report['id'] }}" aria-label="Pilih {{ $report['ticket'] }}"></td>@endunless
                    <td class="px-4 py-5 font-bold whitespace-nowrap text-slate-900">#{{ $report['ticket'] }}</td>
                    <td class="px-4 py-5">{{ $report['type_label'] }}</td><td class="px-4 py-5"><span class="rounded bg-slate-100 px-2 py-1.5 ">{{ $report['role'] }}</span>
                        @unless($archive)<small class="report-cell-meta">Ingin dihubungi: {{ $report['wants_contact'] ? 'Ya' : 'Tidak' }}</small>@endunless
                    </td>
                    @if($archive)
                        <td class="px-4 py-5">{{ $report['reason'] }}</td><td class="px-4 py-5 whitespace-nowrap">{{ $report['received'] }}</td><td class="px-4 py-5 whitespace-nowrap">{{ $report['archived_at'] }}</td>
                    @else
                        <td class="px-4 py-5"><span class="bk-report-snippet text-slate-500">{{ $report['snippet'] }}</span></td>
                        <td class="px-4 py-5 whitespace-nowrap">{{ $report['received'] }}<small class="report-cell-meta">Ditangani: {{ $report['assigned'] }}</small></td>
                        <td class="px-4 py-5"><x-kesiswaan.urgency :value="$report['urgency']" /></td>
                    @endif
                    <td class="px-4 py-5"><a href="{{ route('kesiswaan.report', $report['id']) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-blue-700 font-semibold whitespace-nowrap hover:bg-blue-100">Buka<span class="material-symbols-outlined text-lg">arrow_forward</span></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <p x-cloak x-show="visibleCount === 0" class="p-10 text-center text-sm text-slate-500">Tidak ada laporan yang sesuai dengan filter ini.</p>
    <div class="staff-desktop-only border-t border-slate-100 px-6 py-4 text-xs text-slate-500">Menampilkan <span x-text="visibleCount">{{ $reports->count() }}</span> dari {{ $reports->count() }} laporan yang dapat Anda akses.</div>
</section>
@unless($archive)
<template x-teleport="body">
    <div x-cloak x-show="modal === 'bulk-iseng'" x-transition.opacity class="bk-modal fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" @click.self="closeModal()" @keydown.escape.window="closeModal()">
        <section id="bulk-iseng" role="dialog" aria-modal="true" aria-labelledby="bulk-iseng-title" tabindex="-1" @keydown.tab="trapFocus($event)" class="w-full max-w-lg max-h-[calc(100dvh-2rem)] overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
            <h2 id="bulk-iseng-title" class="text-lg font-bold text-slate-900">Tandai laporan sebagai iseng?</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">Anda memilih <strong x-text="selected.length"></strong> laporan. Laporan yang ditandai iseng akan dipindahkan dari antrean aktif ke arsip.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="closeModal()" class="rounded-xl border border-slate-200 px-4 py-2.5 hover:bg-slate-50">Batal</button>
                <form method="POST" action="{{ route('kesiswaan.reports.bulk-iseng') }}">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" class="rounded-xl bg-primary text-on-primary px-4 py-2.5 hover:bg-primary-container">Konfirmasi</button>
                </form>
            </div>
        </section>
    </div>
</template>
@endunless
