<x-layouts.bk title="Dashboard BK" page="dashboard" mode="dashboard">
<main x-data="bkTable('dashboard')" @bk-search.window="query = $event.detail.query; statusTab = 0; selected = []" class="bk-reports-page p-8 w-full source-space-y-6">
<section class="grid grid-cols-1 md:grid-cols-3 gap-5">
<div class="bk-hover-card bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
<div>
<span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Belum Dibaca</span>
<div class="flex items-baseline gap-2 mt-1">
<span class="text-3xl font-bold text-slate-900 tracking-tight">14</span>
<span class="text-xs text-slate-500 font-medium">laporan baru</span>
</div>
<p class="text-[11px] text-slate-400 mt-1">Menunggu penelaahan awal konselor</p>
</div>
<div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
<span class="material-symbols-outlined text-[24px]">mark_email_unread</span>
</div>
</div>
<div class="relative overflow-hidden bg-rose-50/70 rounded-xl p-5 border-2 border-rose-400 shadow-xs shadow-rose-200/50 flex items-center justify-between">
<div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rose-200/40 rounded-full blur-xl pointer-events-none">
</div>
<div>
<div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-bold uppercase tracking-wider shadow-xs">
<span class="w-1.5 h-1.5 rounded-full bg-white animate-ping">
</span>
              Perhatian Utama
            </div>
<div class="text-xs font-semibold text-rose-900 mt-1.5">Danger Flag Belum Dibaca</div>
<div class="flex items-baseline gap-2 mt-0.5">
<span class="text-3xl font-bold text-rose-600 tracking-tight">3</span>
<span class="text-xs font-semibold text-rose-700">laporan darurat</span>
</div>
<p class="text-[11px] text-rose-700/90 font-medium mt-0.5">Indikasi bahaya fisik / intimidasi intens</p>
</div>
<div class="w-12 h-12 rounded-xl bg-rose-600 text-white flex items-center justify-center shadow-md shadow-rose-500/30">
<span class="material-symbols-outlined text-[26px] fill-1">e911_emergency</span>
</div>
</div>
<div class="bk-hover-card bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
<div>
<span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tanpa Update &gt; 3 Hari</span>
<div class="flex items-baseline gap-2 mt-1">
<span class="text-3xl font-bold text-slate-900 tracking-tight">5</span>
<span class="text-xs text-amber-700 font-medium">perlu tindak lanjut</span>
</div>
<p class="text-[11px] text-slate-400 mt-1">Penanganan belum diperbarui minggu ini</p>
</div>
<div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
<span class="material-symbols-outlined text-[24px]">history_toggle_off</span>
</div>
</div>
</section>
<section class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
<div class="px-6 pt-5 pb-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
<div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-lg">
<button   class="tab-btn px-4 py-1.5 rounded-md text-xs transition-all flex items-center gap-1.5 text-slate-500 font-medium" type="button" @click="statusTab = 0; selected = []" :class="{ 'bk-tab-active': statusTab === 0 }" :aria-pressed="statusTab === 0">
<span>Belum Dibaca</span>
<span class="px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold">14</span>
</button>
<button   class="tab-btn px-4 py-1.5 rounded-md text-xs hover:text-slate-800 transition-all flex items-center gap-1.5 text-slate-500 font-medium" type="button" @click="statusTab = 1; selected = []" :class="{ 'bk-tab-active': statusTab === 1 }" :aria-pressed="statusTab === 1">
<span>Sedang Ditangani</span>
<span class="px-1.5 py-0.2 rounded-full bg-slate-200 text-slate-600 text-[10px]">8</span>
</button>
<button   class="tab-btn px-4 py-1.5 rounded-md text-xs hover:text-slate-800 transition-all flex items-center gap-1.5 text-slate-500 font-medium" type="button" @click="statusTab = 2; selected = []" :class="{ 'bk-tab-active': statusTab === 2 }" :aria-pressed="statusTab === 2">
<span>Selesai</span>
<span class="px-1.5 py-0.2 rounded-full bg-slate-200 text-slate-600 text-[10px]">27</span>
</button>
</div>
<div class="flex items-center gap-3">
<div x-cloak x-show="selected.length > 0" x-transition.opacity class="text-xs text-slate-600 font-medium " id="selectionCounter">
<span x-text="selected.length" class="font-bold text-slate-900" id="selectedCount">1</span> laporan dipilih
            </div>
<button @click="openModal('isengModal')" :disabled="selected.length === 0" :class="{ 'bk-bulk-active': selected.length > 0 }"  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all border border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed" id="bulkActionBtn" type="button">
<span class="material-symbols-outlined text-[16px]">report</span>
<span>Tandai Iseng</span>
</button>
</div>
</div>
<div class="px-6 py-3.5 bg-slate-50/70 border-b border-slate-200/80 flex flex-wrap items-center gap-2.5">
<div class="relative flex-1 min-w-[240px]">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
<input x-model="query" @input="selected = []" aria-label="Cari data tabel" class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200/60 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all" placeholder="Cari kode tiket (cth: SR-2024-8042)..." type="text">
</div>
<div class="relative inline-flex items-center rounded-lg border border-slate-200/60 bg-white transition-all group">
<span class="material-symbols-outlined pl-2.5 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">category</span>
<select  class="appearance-none pl-2 pr-8 py-2 bg-transparent text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 rounded-lg cursor-pointer" style="border-width: medium; border-style: none; border-color: currentcolor; border-image: none; outline: none; box-shadow: none;" @change="filterChanged($event)">
<option value="">Jenis: Semua</option>
<option value="fisik">Fisik</option>
<option value="verbal">Ejekan &amp; Verbal</option>
<option value="dikucilkan">Dikucilkan</option>
<option value="cyber">Cyberbullying</option>
<option value="pelecehan">Pelecehan</option>
<option value="lainnya">Lainnya</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">expand_more</span>
</div>
<div class="relative inline-flex items-center rounded-lg border border-slate-200/60 bg-white transition-all group">
<span class="material-symbols-outlined pl-2.5 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">badge</span>
<select  class="appearance-none pl-2 pr-8 py-2 bg-transparent text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 rounded-lg cursor-pointer" style="border-width: medium; border-style: none; border-color: currentcolor; border-image: none; outline: none; box-shadow: none;" @change="filterChanged($event)">
<option value="">Peran: Semua</option>
<option value="siswa">Siswa</option>
<option value="ortu">Orang Tua</option>
<option value="teman">Teman Korban</option>
<option value="guru">Guru</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">expand_more</span>
</div>
<div class="relative inline-flex items-center rounded-lg border border-slate-200/60 bg-white transition-all group">
<span class="material-symbols-outlined pl-2.5 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">flag</span>
<select  class="appearance-none pl-2 pr-8 py-2 bg-transparent text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 rounded-lg cursor-pointer" style="border-width: medium; border-style: none; border-color: currentcolor; border-image: none; outline: none; box-shadow: none;" @change="filterChanged($event)">
<option value="">Urgensi: Semua</option>
<option value="urgent">Urgent / Darurat</option>
<option value="medium">Medium</option>
<option value="easy">Easy</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">expand_more</span>
</div>
<div class="relative inline-flex items-center rounded-lg border border-slate-200 bg-white transition-all group">
<span class="material-symbols-outlined pl-2.5 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">contact_phone</span>
<select  class="appearance-none pl-2 pr-8 py-2 bg-transparent text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 rounded-lg cursor-pointer border-0" style="border-width: 0; outline: none; box-shadow: none;" @change="filterChanged($event)">
<option value="">Kontak: Semua</option>
<option value="ya">Ingin Dihubungi (Ya)</option>
<option value="tidak">Tidak Ingin Dihubungi</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">expand_more</span>
</div>
<div class="relative inline-flex items-center rounded-lg border bg-white transition-all group border-slate-200">
<span class="material-symbols-outlined pl-2.5 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">calendar_today</span>
<select  class="appearance-none pl-2 pr-8 py-2 bg-transparent text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 rounded-lg cursor-pointer border-0" style="border-width: 0; outline: none; box-shadow: none;" @change="filterChanged($event)">
<option value="">Waktu: Semua</option>
<option value="7">7 Hari Terakhir</option>
<option value="30">30 Hari Terakhir</option>
<option value="custom">Kustom</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">expand_more</span>
</div>
</div>
<x-bk.date-filter />
<div class="bk-reports-scroll overflow-x-auto" tabindex="0" role="region" aria-label="Tabel laporan, geser horizontal bila diperlukan">
<table class="bk-reports-table w-full text-left border-collapse">
<thead>
<tr class="border-b border-slate-200 bg-slate-50/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 select-none">
<th class="py-3 px-4 w-10 text-center">
<input @change="selectVisible($event.target.checked)" :checked="allSelected" :indeterminate.prop="selected.length > 0 && !allSelected" aria-label="Pilih semua laporan yang terlihat" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" id="selectAll" type="checkbox">
</th>
<th class="py-3 px-3">Kode Tiket</th>
<th class="py-3 px-3">Urgensi</th>
<th class="py-3 px-3">Jenis Kejadian</th>
<th class="py-3 px-3">Peran Pelapor</th>
<th class="py-3 px-3 min-w-[280px]">Cuplikan Cerita</th>
<th class="py-3 px-3 whitespace-nowrap">Waktu</th>
<th class="py-3 px-3">Ingin Dihubungi</th>
<th class="py-3 px-3">Ditangani Oleh</th>
<th class="py-3 px-4 text-right">Aksi</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 text-xs text-slate-700">
<tr  class="hover:bg-rose-50/40 transition-colors bg-rose-50/20"  data-preview-row="0" x-show="rowVisible(0)">
<td class="py-3 px-4 text-center">
<input x-model="selected" value="0" aria-label="Pilih laporan" class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" type="checkbox">
</td>
<td class="py-3 px-3">
<div class="flex items-center gap-2 font-bold text-slate-900 tracking-tight">
<span class="w-2 h-2 rounded-full bg-blue-600 ring-2 ring-blue-100" title="Belum dibaca">
</span>
<span>#SR-8042</span>
</div>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
<span class="material-symbols-outlined text-[15px] fill-1 text-rose-600">error</span>
<span>Darurat</span>
</span>
</td>
<td class="py-3 px-3 font-medium text-slate-800 whitespace-nowrap">
                  Bullying Fisik
                </td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-[11px]">
<span class="material-symbols-outlined text-[13px] text-slate-500">person</span>
                    Siswa
                  </span>
</td>
<td class="py-3 px-3 text-slate-600">
<p class="bk-report-snippet" title="Ditarik dan diancam di lorong kelas 8 saat istirahat, pelaku membawa benda tumpul...">
                    Ditarik dan diancam di lorong kelas 8 saat istirahat, pelaku membawa benda tumpul...
                  </p>
</td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap font-medium">
                  24 Okt • Istirahat 1
                </td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="material-symbols-outlined text-[13px]">chat</span>
                    Ya (WhatsApp)
                  </span>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="text-slate-400 italic text-[11px]">Belum Ditugaskan</span>
</td>
<td class="py-3 px-4 text-right whitespace-nowrap">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-all"  href="{{ route('bk.report') }}">
<span>Buka Laporan</span>
<span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
<tr  class="hover:bg-amber-50/30 transition-colors bg-amber-50/15" data-preview-row="1" x-show="rowVisible(1)">
<td class="py-3 px-4 text-center">
<input x-model="selected" value="1" aria-label="Pilih laporan" class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" type="checkbox">
</td>
<td class="py-3 px-3">
<div class="flex items-center gap-2 font-bold text-slate-900 tracking-tight">
<span class="w-2 h-2 rounded-full bg-blue-600 ring-2 ring-blue-100" title="Belum dibaca">
</span>
<span>#SR-8041</span>
</div>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
<span class="material-symbols-outlined text-[15px] fill-1 text-amber-600">warning</span>
<span>Medium</span>
</span>
</td>
<td class="py-3 px-3 font-medium text-slate-800 whitespace-nowrap">
                  Intimidasi Verbal
                </td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-[11px]">
<span class="material-symbols-outlined text-[13px] text-slate-500">group</span>
                    Teman Korban
                  </span>
</td>
<td class="py-3 px-3 text-slate-600">
<p class="bk-report-snippet" title="Ancaman kekerasan berulang di grup chat angkatan serta pemalakan di kantin belakang...">
                    Ancaman kekerasan berulang di grup chat angkatan serta pemalakan di kantin belakang...
                  </p>
</td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap font-medium">
                  24 Okt • Pagi
                </td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="material-symbols-outlined text-[13px]">chat</span>
                    Ya (WhatsApp)
                  </span>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<div class="flex items-center gap-1.5">
<span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center">SJ</span>
<span class="font-medium text-slate-800">Sarah Jenkins</span>
</div>
</td>
<td class="py-3 px-4 text-right whitespace-nowrap">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-all"  href="{{ route('bk.report') }}" @click.prevent="previewOtherReport($el.href)">
<span>Buka Laporan</span>
<span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
<tr  class="hover:bg-slate-50 transition-colors" data-preview-row="2" x-show="rowVisible(2)">
<td class="py-3 px-4 text-center">
<input x-model="selected" value="2" aria-label="Pilih laporan" class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" type="checkbox">
</td>
<td class="py-3 px-3">
<div class="flex items-center gap-2 font-bold text-slate-900 tracking-tight">
<span class="w-2 h-2 rounded-full bg-blue-600 ring-2 ring-blue-100" title="Belum dibaca">
</span>
<span>#SR-8039</span>
</div>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
<span class="material-symbols-outlined text-[15px] fill-1 text-rose-600">error</span>
<span>Darurat</span>
</span>
</td>
<td class="py-3 px-3 font-medium text-slate-800 whitespace-nowrap">
                  Cyberbullying
                </td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-[11px]">
<span class="material-symbols-outlined text-[13px] text-slate-500">family_restroom</span>
                    Orang Tua-Wali
                  </span>
</td>
<td class="py-3 px-3 text-slate-600">
<p class="bk-report-snippet" title="Penyebaran foto editan tak pantas di media sosial dengan narasi merendahkan nama baik...">
                    Penyebaran foto editan tak pantas di media sosial dengan narasi merendahkan nama baik...
                  </p>
</td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap font-medium">
                  23 Okt • Siang
                </td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="material-symbols-outlined text-[13px]">chat</span>
                    Ya (WhatsApp)
                  </span>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="text-slate-400 italic text-[11px]">Belum Ditugaskan</span>
</td>
<td class="py-3 px-4 text-right whitespace-nowrap">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-all"  href="{{ route('bk.report') }}" @click.prevent="previewOtherReport($el.href)">
<span>Buka Laporan</span>
<span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
<tr  class="hover:bg-slate-50 transition-colors" data-preview-row="3" x-show="rowVisible(3)">
<td class="py-3 px-4 text-center">
<input x-model="selected" value="3" aria-label="Pilih laporan" class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" type="checkbox">
</td>
<td class="py-3 px-3">
<div class="flex items-center gap-2 font-bold text-slate-900 tracking-tight">
<span class="w-2 h-2 rounded-full bg-blue-600 ring-2 ring-blue-100" title="Belum dibaca">
</span>
<span>#SR-8036</span>
</div>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
<span class="material-symbols-outlined text-[15px] fill-1 text-amber-600">warning</span>
<span>Medium</span>
</span>
</td>
<td class="py-3 px-3 font-medium text-slate-800 whitespace-nowrap">
                  Pengucilan
                </td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-[11px]">
<span class="material-symbols-outlined text-[13px] text-slate-500">visibility</span>
                    Saksi
                  </span>
</td>
<td class="py-3 px-3 text-slate-600">
<p class="bk-report-snippet" title="Siswa sengaja dikeluarkan dari seluruh kelompok belajar dan meja belajarnya disingkirkan...">
                    Siswa sengaja dikeluarkan dari seluruh kelompok belajar dan meja belajarnya disingkirkan...
                  </p>
</td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap font-medium">
                  23 Okt • Sore
                </td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500">
                    Tidak
                  </span>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="text-slate-400 italic text-[11px]">Belum Ditugaskan</span>
</td>
<td class="py-3 px-4 text-right whitespace-nowrap">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-all"  href="{{ route('bk.report') }}" @click.prevent="previewOtherReport($el.href)">
<span>Buka Laporan</span>
<span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
<tr  class="hover:bg-slate-50 transition-colors" data-preview-row="4" x-show="rowVisible(4)">
<td class="py-3 px-4 text-center">
<input x-model="selected" value="4" aria-label="Pilih laporan" class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" type="checkbox">
</td>
<td class="py-3 px-3">
<div class="flex items-center gap-2 font-bold text-slate-900 tracking-tight">
<span class="w-2 h-2 rounded-full bg-blue-600 ring-2 ring-blue-100" title="Belum dibaca">
</span>
<span>#SR-8032</span>
</div>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="material-symbols-outlined text-[15px] fill-1 text-emerald-600">check_circle</span>
<span>Easy</span>
</span>
</td>
<td class="py-3 px-3 font-medium text-slate-800 whitespace-nowrap">
                  Ejekan &amp; Verbal
                </td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-[11px]">
<span class="material-symbols-outlined text-[13px] text-slate-500">person</span>
                    Siswa
                  </span>
</td>
<td class="py-3 px-3 text-slate-600">
<p class="bk-report-snippet" title="Panggilan bernada menghina kondisi fisik berulang kali saat pergantian jam pelajaran...">
                    Panggilan bernada menghina kondisi fisik berulang kali saat pergantian jam pelajaran...
                  </p>
</td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap font-medium">
                  22 Okt • Pagi
                </td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="material-symbols-outlined text-[13px]">chat</span>
                    Ya (WhatsApp)
                  </span>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<div class="flex items-center gap-1.5">
<span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center">SJ</span>
<span class="font-medium text-slate-800">Sarah Jenkins</span>
</div>
</td>
<td class="py-3 px-4 text-right whitespace-nowrap">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-all"  href="{{ route('bk.report') }}" @click.prevent="previewOtherReport($el.href)">
<span>Buka Laporan</span>
<span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
<tr  class="hover:bg-slate-50 transition-colors" data-preview-row="5" x-show="rowVisible(5)">
<td class="py-3 px-4 text-center">
<input x-model="selected" value="5" aria-label="Pilih laporan" class="row-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" type="checkbox">
</td>
<td class="py-3 px-3">
<div class="flex items-center gap-2 font-bold text-slate-900 tracking-tight">
<span class="w-2 h-2 rounded-full bg-blue-600 ring-2 ring-blue-100" title="Belum dibaca">
</span>
<span>#SR-8025</span>
</div>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="material-symbols-outlined text-[15px] fill-1 text-emerald-600">check_circle</span>
<span>Easy</span>
</span>
</td>
<td class="py-3 px-3 font-medium text-slate-800 whitespace-nowrap">
                  Lainnya
                </td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap">
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-[11px]">
<span class="material-symbols-outlined text-[13px] text-slate-500">school</span>
                    Guru
                  </span>
</td>
<td class="py-3 px-3 text-slate-600">
<p class="bk-report-snippet" title="Perilaku menarik diri yang drastis di area perpustakaan setelah jam pulang sekolah...">
                    Perilaku menarik diri yang drastis di area perpustakaan setelah jam pulang sekolah...
                  </p>
</td>
<td class="py-3 px-3 text-slate-600 whitespace-nowrap font-medium">
                  20 Okt • Sore
                </td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500">
                    Tidak
                  </span>
</td>
<td class="py-3 px-3 whitespace-nowrap">
<span class="text-slate-400 italic text-[11px]">Belum Ditugaskan</span>
</td>
<td class="py-3 px-4 text-right whitespace-nowrap">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-all"  href="{{ route('bk.report') }}" @click.prevent="previewOtherReport($el.href)">
<span>Buka Laporan</span>
<span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</td>
</tr>
<tr x-cloak x-show="visibleCount === 0">
<td colspan="10" class="p-8 text-center text-sm text-slate-500">Tidak ada data contoh yang cocok dengan pilihan ini.</td>
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
<span x-text="'Menampilkan ' + visibleCount + ' dari ' + rows.length + ' data contoh'">
</span>
</span>
</div>
</div>
<div class="flex items-center gap-1.5 select-none">
<div class="flex items-center gap-3 select-none">
<div class="flex items-center gap-1.5">
<span class="text-slate-400 font-medium">Tampilkan:</span>
<div class="relative inline-flex items-center rounded-lg border border-slate-200 bg-white hover:border-slate-300 hover:shadow-xs transition-all group">
<select  class="appearance-none bg-transparent hover:bg-slate-50/50 text-slate-700 text-xs font-semibold rounded-lg pl-3 pr-7 py-1.5 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all border-0" style="border: none; outline: none; box-shadow: none;" @change="filterChanged($event)">
<option value="6">6 / halaman</option>
<option value="10">10 / halaman</option>
<option value="25">25 / halaman</option>
</select>
<span class="material-symbols-outlined absolute right-1.5 top-1/2 -translate-y-1/2 text-slate-400 text-[16px] pointer-events-none group-hover:text-slate-600 transition-colors">expand_more</span>
</div>
</div>
<div class="flex items-center gap-1">
<button  type="button" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50 hover:text-slate-600 transition-all" disabled="" @click="unavailable('Halaman data berikutnya')">
<span class="material-symbols-outlined text-[16px]">chevron_left</span>
</button>
<span class="px-2 text-xs font-semibold text-slate-700">1 / 3</span>
<button  type="button" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all" @click="unavailable('Halaman data berikutnya')">
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
<button @click="closeModal()"  class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors" id="cancelModalBtn" type="button">
            Batal
          </button>
<button @click="unavailable('Penandaan laporan iseng')"  class="px-4 py-2 rounded-lg text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white transition-colors shadow-xs" id="confirmModalBtn" type="button">
            Ya, Tandai Iseng
          </button>
</div>
</div>
</div>
</div>
</template>
</main>
</x-layouts.bk>

