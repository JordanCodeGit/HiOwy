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
        <label><span class="sr-only">Jenis kejadian</span><select x-model="filters.type" @change="selected = []" class="rounded-lg border-slate-200 bg-white py-2 text-sm"><option value="">Jenis: Semua</option>@foreach($types as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></label>
        @if($archive)
        <label><span class="sr-only">Alasan arsip</span><select x-model="filters.reason" class="rounded-lg border-slate-200 bg-white py-2 text-sm"><option value="">Alasan: Semua</option>@foreach($reports->pluck('reason')->unique() as $reason)<option>{{ $reason }}</option>@endforeach</select></label>
        @else
        <label><span class="sr-only">Peran pelapor</span><select x-model="filters.role" @change="selected = []" class="rounded-lg border-slate-200 bg-white py-2 text-sm"><option value="">Peran: Semua</option>@foreach($reports->pluck('role')->unique() as $role)<option>{{ $role }}</option>@endforeach</select></label>
        <label><span class="sr-only">Urgensi</span><select x-model="filters.urgency" @change="selected = []" class="rounded-lg border-slate-200 bg-white py-2 text-sm"><option value="">Urgensi: Semua</option><option>Darurat</option><option>Medium</option><option>Easy</option></select></label>
        <label><span class="sr-only">Ingin dihubungi</span><select x-model="filters.contact" @change="selected = []" class="rounded-lg border-slate-200 bg-white py-2 text-sm"><option value="">Ingin dihubungi: Semua</option><option value="ya">Ya</option><option value="tidak">Tidak</option></select></label>
        @endif
        <label><span class="sr-only">Periode</span><select x-model="period" @change="selected = []" class="rounded-lg border-slate-200 bg-white py-2 text-sm"><option value="">Waktu: Semua</option><option value="2024-10-24">24 Okt 2024</option><option value="2024-10-18">18–24 Okt 2024</option><option value="custom">Rentang tanggal</option></select></label>
        <div x-cloak x-show="period === 'custom'" class="flex flex-wrap gap-2"><label class="text-xs">Dari <input type="date" x-model="dateFrom" @change="selected = []" class="rounded-lg border-slate-200 text-sm"></label><label class="text-xs">Sampai <input type="date" x-model="dateTo" @change="selected = []" class="rounded-lg border-slate-200 text-sm"></label></div>
    </div>
    <div class="bk-reports-scroll overflow-x-auto">
        <table class="bk-reports-table w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wide"><tr>
                @unless($archive)<th class="p-4"><input type="checkbox" aria-label="Pilih semua laporan yang terlihat" :checked="allSelected" @change="selectVisible($event.target.checked)"></th>@endunless
                @foreach($archive ? ['Kode Tiket', 'Jenis Kejadian', 'Peran Pelapor', 'Alasan Arsip', 'Waktu Masuk', 'Diarsipkan', 'Aksi'] : ['Kode Tiket', 'Urgensi', 'Jenis Kejadian', 'Peran Pelapor', 'Cuplikan Cerita', 'Waktu', 'Ingin Dihubungi', 'Ditangani Oleh', 'Aksi'] as $heading)<th scope="col" class="px-4 py-3 whitespace-nowrap font-bold">{{ $heading }}</th>@endforeach
            </tr></thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
            @foreach($reports as $report)
                <tr data-report-row data-id="{{ $report['id'] }}" data-type="{{ $report['type'] }}" data-role="{{ $report['role'] }}" data-urgency="{{ $report['urgency'] }}" data-contact="{{ $report['wants_contact'] ? 'ya' : 'tidak' }}" data-status="{{ $report['status'] }}" data-reason="{{ $report['reason'] }}" data-date="{{ $report['date'] }}" x-show="rowVisible({{ $loop->index }})" class="hover:bg-blue-50/40 transition-colors">
                    @unless($archive)<td class="p-4"><input type="checkbox" x-model="selected" value="{{ $report['id'] }}" aria-label="Pilih {{ $report['ticket'] }}"></td>@endunless
                    <td class="px-4 py-5 font-bold whitespace-nowrap text-slate-900">#{{ $report['ticket'] }}</td>
                    @unless($archive)<td class="px-4 py-5"><x-kesiswaan.urgency :value="$report['urgency']" /></td>@endunless
                    <td class="px-4 py-5">{{ $report['type_label'] }}</td><td class="px-4 py-5"><span class="rounded bg-slate-100 px-2 py-1.5 whitespace-nowrap">{{ $report['role'] }}</span></td>
                    @if($archive)
                        <td class="px-4 py-5">{{ $report['reason'] }}</td><td class="px-4 py-5 whitespace-nowrap">{{ $report['received'] }}</td><td class="px-4 py-5 whitespace-nowrap">{{ $report['archived_at'] }}</td>
                    @else
                        <td class="px-4 py-5"><span class="bk-report-snippet text-slate-500">{{ $report['snippet'] }}</span></td><td class="px-4 py-5 whitespace-nowrap">{{ $report['received'] }}</td>
                        <td class="px-4 py-5"><span @class(['rounded-full px-3 py-1 text-xs', 'bg-emerald-50 text-emerald-700' => $report['wants_contact'], 'bg-slate-100 text-slate-500' => !$report['wants_contact']])>{{ $report['wants_contact'] ? 'Ya' : 'Tidak' }}</span></td><td class="px-4 py-5">{{ $report['assigned'] }}</td>
                    @endif
                    <td class="px-4 py-5"><a href="{{ route('kesiswaan.report', $report['id']) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-blue-700 font-semibold whitespace-nowrap hover:bg-blue-100">Buka Laporan<span class="material-symbols-outlined text-lg">arrow_forward</span></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <p x-cloak x-show="visibleCount === 0" class="p-10 text-center text-sm text-slate-500">Tidak ada laporan yang sesuai dengan filter ini.</p>
    <div class="border-t border-slate-100 px-6 py-4 text-xs text-slate-500">Menampilkan <span x-text="visibleCount">{{ $reports->count() }}</span> dari {{ $reports->count() }} laporan yang dapat Anda akses.</div>
</section>
@unless($archive)
<x-kesiswaan.confirmation id="bulk-iseng" title="Tandai laporan sebagai iseng?" action="Penandaan laporan iseng">Anda memilih <span x-text="selected.length"></span> laporan. Penandaan akan memengaruhi status laporan. Konfirmasi untuk melanjutkan.</x-kesiswaan.confirmation>
@endunless
