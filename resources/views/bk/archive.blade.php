<x-layouts.bk title="Arsip Laporan" page="archive" mode="">
<main x-data="bkTable('archive')" class="relative  w-full min-h-screen bg-background">
<div class="flex flex-col w-full">
<div class="px-8 py-8 flex flex-col gap-6 max-w-7xl mx-auto w-full">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
<div class="flex flex-col gap-1.5">
<div class="inline-flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
<span class="w-1.5 h-1.5 rounded-full bg-secondary">
</span>
          Modul Konseling &amp; Investigasi
        </div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight font-bold">
          Arsip Laporan
        </h1>
<p class="font-body-md text-body-md text-on-surface-variant">
          Laporan terselesaikan dan ditandai iseng. Retensi otomatis oleh sistem akhir semester.
        </p>
</div>
<div class="flex items-center gap-3 self-start md:self-auto">
<div class="bg-surface-container-lowest px-4 py-2.5 rounded-xl shadow-xs flex items-center gap-3">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">folder_managed</span>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Total Terarsip</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold leading-none mt-0.5">48 Tiket</span>
</div>
</div>
<div class="bg-surface-container-lowest px-4 py-2.5 rounded-xl shadow-xs flex items-center gap-3">
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-on-tertiary-container">
<span class="material-symbols-outlined text-xl">verified</span>
</div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Terselesaikan</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold leading-none mt-0.5">41 Kasus</span>
</div>
</div>
</div>
</div>
<div class="bg-surface-container-lowest px-4 py-3 rounded-xl shadow-xs flex items-center justify-between gap-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-secondary-fixed flex items-center justify-center text-secondary shrink-0">
<span class="material-symbols-outlined text-lg">auto_delete</span>
</div>
<div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
<span class="font-label-md text-label-md text-on-surface font-semibold">Kebijakan Retensi:</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Arsip dibersihkan otomatis oleh sistem pada penutupan tahun ajaran (30 Juni 2025).</span>
</div>
</div>
<div class="hidden lg:flex items-center gap-1.5 px-2.5 py-1 bg-surface-container-low rounded-lg text-on-surface-variant font-label-sm text-label-sm shrink-0">
<span class="material-symbols-outlined text-sm">lock_clock</span>
        Retensi Otomatis
      </div>
</div>
<div class="bg-surface-container-lowest p-4 rounded-xl shadow-xs flex flex-col xl:flex-row items-stretch xl:items-center justify-between gap-3">
<div class="relative flex-1 min-w-[240px]">
<span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
<input x-model="query" @input="selected = []" aria-label="Cari data tabel" class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low rounded-xl font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-secondary/20 transition-all" id="ticketSearchInput" placeholder="Cari kode tiket (SR-2024-XXXX)..." type="text">
</div>
<div class="flex flex-wrap items-center gap-2.5">
<div class="relative min-w-[150px] flex-1 sm:flex-initial">
<select  class="w-full appearance-none pl-3 pr-8 py-2.5 bg-surface-container-low rounded-xl font-body-sm text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all cursor-pointer" id="filterReason" @change="filterChanged($event)">
<option value="all">Alasan: Semua</option>
<option value="resolved">Selesai Ditangani</option>
<option value="spam">Ditandai Iseng</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-outline text-base pointer-events-none">expand_more</span>
</div>
<div class="relative min-w-[160px] flex-1 sm:flex-initial">
<select  class="w-full appearance-none pl-3 pr-8 py-2.5 bg-surface-container-low rounded-xl font-body-sm text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all cursor-pointer" id="filterType" @change="filterChanged($event)">
<option value="all">Jenis Kejadian</option>
<option value="physical">Bullying Fisik</option>
<option value="verbal">Intimidasi Verbal</option>
<option value="cyber">Cyberbullying</option>
<option value="exclusion">Pengucilan</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-outline text-base pointer-events-none">expand_more</span>
</div>
<div class="relative min-w-[160px] flex-1 sm:flex-initial">
<button  class="w-full flex items-center justify-between gap-2 px-3 py-2.5 bg-surface-container-low rounded-xl font-body-sm text-body-sm text-on-surface hover:bg-surface-container transition-colors" type="button" @click="dateFilterOpen = !dateFilterOpen" :aria-expanded="dateFilterOpen">
<span class="inline-flex items-center gap-2">
<span class="material-symbols-outlined text-outline text-base">date_range</span>
              Rentang Tanggal
            </span>
<span class="material-symbols-outlined text-outline text-base">expand_more</span>
</button>
</div>
</div>
</div>
<div class="bg-surface-container-lowest rounded-xl shadow-xs overflow-hidden flex flex-col">
<x-bk.date-filter />
<div class="overflow-x-auto">
<table class="w-full text-left whitespace-nowrap">
<thead>
<tr class="bg-surface-container-low/70 text-on-surface-variant font-label-md text-label-md select-none">
<th class="py-3.5 pl-6 pr-3 w-10" scope="col">
<input @change="selectVisible($event.target.checked)" :checked="allSelected" :indeterminate.prop="selected.length > 0 && !allSelected" aria-label="Pilih semua laporan yang terlihat" class="w-4 h-4 rounded text-secondary bg-surface-container-lowest focus:ring-secondary/20 transition cursor-pointer" id="selectAllCheckbox" type="checkbox">
</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Kode Tiket</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Jenis Kejadian</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Peran Pelapor</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Alasan Arsip</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Tanggal Masuk</th>
<th class="py-3.5 px-4 font-semibold" scope="col">Tanggal Diarsipkan</th>
<th class="py-3.5 pl-4 pr-6 text-right font-semibold" scope="col">Aksi</th>
</tr>
</thead>
<tbody class="font-body-md text-body-md text-on-surface" id="archiveTableBody">
<tr  class="hover:bg-surface-container-low/40 transition-colors group" data-preview-row="0" x-show="rowVisible(0)">
<td class="py-4 pl-6 pr-3">
<input x-model="selected" value="0" aria-label="Pilih laporan" class="row-checkbox w-4 h-4 rounded text-secondary bg-surface-container-lowest focus:ring-secondary/20 transition cursor-pointer" type="checkbox">
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-body-md font-bold tracking-tight text-on-surface">#SR-2024-8012</span>
<button  class="opacity-0 group-hover:opacity-100 text-outline hover:text-on-surface transition-opacity p-0.5" title="Salin kode" type="button" @click="copyText($el.closest('td').textContent.replace('content_copy', '').trim())">
<span class="material-symbols-outlined text-sm">content_copy</span>
</button>
</div>
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2 font-medium">
<span class="w-2 h-2 rounded-full bg-secondary">
</span>
                  Bullying Fisik
                </div>
</td>
<td class="py-4 px-4 text-on-surface-variant font-medium">
                Siswa
              </td>
<td class="py-4 px-4">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm bg-surface-container text-on-tertiary-container font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container">
</span>
                  Selesai Ditangani
                </span>
</td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
                12 Okt 2024, 09:14
              </td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
                24 Okt 2024, 15:30
              </td>
<td class="py-4 pl-4 pr-6 text-right">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 bg-surface-container-low hover:bg-primary hover:text-on-primary rounded-lg font-label-sm text-label-sm text-on-surface transition-all"  href="#"  @click.prevent="previewOtherReport($el.dataset.url)" data-url="{{ route('bk.report') }}">
                  Buka Laporan
                  <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/40 transition-colors group" data-preview-row="1" x-show="rowVisible(1)">
<td class="py-4 pl-6 pr-3">
<input x-model="selected" value="1" aria-label="Pilih laporan" class="row-checkbox w-4 h-4 rounded text-secondary bg-surface-container-lowest focus:ring-secondary/20 transition cursor-pointer" type="checkbox">
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-body-md font-bold tracking-tight text-on-surface">#SR-2024-7994</span>
<button  class="opacity-0 group-hover:opacity-100 text-outline hover:text-on-surface transition-opacity p-0.5" title="Salin kode" type="button" @click="copyText($el.closest('td').textContent.replace('content_copy', '').trim())">
<span class="material-symbols-outlined text-sm">content_copy</span>
</button>
</div>
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2 font-medium">
<span class="w-2 h-2 rounded-full bg-secondary-container">
</span>
                  Intimidasi Verbal
                </div>
</td>
<td class="py-4 px-4 text-on-surface-variant font-medium">
                Teman Korban
              </td>
<td class="py-4 px-4">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm bg-surface-container text-on-tertiary-container font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container">
</span>
                  Selesai Ditangani
                </span>
</td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
                08 Okt 2024, 13:20
              </td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
                19 Okt 2024, 10:15
              </td>
<td class="py-4 pl-4 pr-6 text-right">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 bg-surface-container-low hover:bg-primary hover:text-on-primary rounded-lg font-label-sm text-label-sm text-on-surface transition-all"  href="#"  @click.prevent="previewOtherReport($el.dataset.url)" data-url="{{ route('bk.report') }}">
                  Buka Laporan
                  <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/40 transition-colors group" data-preview-row="2" x-show="rowVisible(2)">
<td class="py-4 pl-6 pr-3">
<input x-model="selected" value="2" aria-label="Pilih laporan" class="row-checkbox w-4 h-4 rounded text-secondary bg-surface-container-lowest focus:ring-secondary/20 transition cursor-pointer" type="checkbox">
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-body-md font-bold tracking-tight text-on-surface">#SR-2024-7950</span>
<button  class="opacity-0 group-hover:opacity-100 text-outline hover:text-on-surface transition-opacity p-0.5" title="Salin kode" type="button" @click="copyText($el.closest('td').textContent.replace('content_copy', '').trim())">
<span class="material-symbols-outlined text-sm">content_copy</span>
</button>
</div>
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2 font-medium">
<span class="w-2 h-2 rounded-full bg-outline">
</span>
                  Cyberbullying
                </div>
</td>
<td class="py-4 px-4 text-on-surface-variant font-medium">
                Siswa
              </td>
<td class="py-4 px-4">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm bg-surface-container-high text-on-surface-variant font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-outline">
</span>
                  Ditandai Iseng
                </span>
</td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
                02 Okt 2024, 21:05
              </td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
                03 Okt 2024, 08:00
              </td>
<td class="py-4 pl-4 pr-6 text-right">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 bg-surface-container-low hover:bg-primary hover:text-on-primary rounded-lg font-label-sm text-label-sm text-on-surface transition-all"  href="#"  @click.prevent="previewOtherReport($el.dataset.url)" data-url="{{ route('bk.report') }}">
                  Buka Laporan
                  <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/40 transition-colors group" data-preview-row="3" x-show="rowVisible(3)">
<td class="py-4 pl-6 pr-3">
<input x-model="selected" value="3" aria-label="Pilih laporan" class="row-checkbox w-4 h-4 rounded text-secondary bg-surface-container-lowest focus:ring-secondary/20 transition cursor-pointer" type="checkbox">
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-body-md font-bold tracking-tight text-on-surface">#SR-2024-7890</span>
<button  class="opacity-0 group-hover:opacity-100 text-outline hover:text-on-surface transition-opacity p-0.5" title="Salin kode" type="button" @click="copyText($el.closest('td').textContent.replace('content_copy', '').trim())">
<span class="material-symbols-outlined text-sm">content_copy</span>
</button>
</div>
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2 font-medium">
<span class="w-2 h-2 rounded-full bg-secondary-fixed-dim">
</span>
                  Pengucilan
                </div>
</td>
<td class="py-4 px-4 text-on-surface-variant font-medium">
                Orang Tua
              </td>
<td class="py-4 px-4">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm bg-surface-container text-on-tertiary-container font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container">
</span>
                  Selesai Ditangani
                </span>
</td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
                25 Sep 2024, 11:42
              </td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
                30 Sep 2024, 16:10
              </td>
<td class="py-4 pl-4 pr-6 text-right">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 bg-surface-container-low hover:bg-primary hover:text-on-primary rounded-lg font-label-sm text-label-sm text-on-surface transition-all"  href="#"  @click.prevent="previewOtherReport($el.dataset.url)" data-url="{{ route('bk.report') }}">
                  Buka Laporan
                  <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</td>
</tr>
<tr  class="hover:bg-surface-container-low/40 transition-colors group" data-preview-row="4" x-show="rowVisible(4)">
<td class="py-4 pl-6 pr-3">
<input x-model="selected" value="4" aria-label="Pilih laporan" class="row-checkbox w-4 h-4 rounded text-secondary bg-surface-container-lowest focus:ring-secondary/20 transition cursor-pointer" type="checkbox">
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2">
<span class="font-headline-sm text-body-md font-bold tracking-tight text-on-surface">#SR-2024-7815</span>
<button  class="opacity-0 group-hover:opacity-100 text-outline hover:text-on-surface transition-opacity p-0.5" title="Salin kode" type="button" @click="copyText($el.closest('td').textContent.replace('content_copy', '').trim())">
<span class="material-symbols-outlined text-sm">content_copy</span>
</button>
</div>
</td>
<td class="py-4 px-4">
<div class="flex items-center gap-2 font-medium">
<span class="w-2 h-2 rounded-full bg-secondary">
</span>
                  Bullying Fisik
                </div>
</td>
<td class="py-4 px-4 text-on-surface-variant font-medium">
                Teman Korban
              </td>
<td class="py-4 px-4">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm bg-surface-container text-on-tertiary-container font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container">
</span>
                  Selesai Ditangani
                </span>
</td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
                18 Sep 2024, 14:10
              </td>
<td class="py-4 px-4 text-on-surface-variant font-body-sm text-body-sm">
                27 Sep 2024, 11:50
              </td>
<td class="py-4 pl-4 pr-6 text-right">
<a  class="inline-flex items-center gap-1 px-3 py-1.5 bg-surface-container-low hover:bg-primary hover:text-on-primary rounded-lg font-label-sm text-label-sm text-on-surface transition-all"  href="#"  @click.prevent="previewOtherReport($el.dataset.url)" data-url="{{ route('bk.report') }}">
                  Buka Laporan
                  <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</td>
</tr>
<tr x-cloak x-show="visibleCount === 0">
<td colspan="10" class="p-8 text-center text-sm text-slate-500">Tidak ada data contoh yang cocok dengan pilihan ini.</td>
</tr>
</tbody>
</table>
</div>
<div class="px-6 py-4 bg-surface-container-lowest flex flex-col sm:flex-row items-center justify-between gap-4">
<div class="font-body-sm text-body-sm text-on-surface-variant">
<span x-text="'Menampilkan ' + visibleCount + ' dari ' + rows.length + ' data contoh'">
</span>
</div>
<div class="flex items-center gap-1.5">
<button  aria-label="Halaman Sebelumnya" class="p-2 rounded-lg text-outline bg-surface-container-low/50 cursor-not-allowed" disabled="" type="button" @click="unavailable('Halaman data berikutnya')">
<span class="material-symbols-outlined text-lg">chevron_left</span>
</button>
<button  class="w-8 h-8 rounded-lg bg-primary text-on-primary font-label-sm text-label-sm font-semibold flex items-center justify-center shadow-xs" type="button" @click="unavailable('Halaman data berikutnya')">
            1
          </button>
<button  class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface font-label-sm text-label-sm font-semibold flex items-center justify-center transition-colors" type="button" @click="unavailable('Halaman data berikutnya')">
            2
          </button>
<button  class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface font-label-sm text-label-sm font-semibold flex items-center justify-center transition-colors" type="button" @click="unavailable('Halaman data berikutnya')">
            3
          </button>
<span class="px-1 text-outline font-label-sm text-label-sm">...</span>
<button  class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface font-label-sm text-label-sm font-semibold flex items-center justify-center transition-colors" type="button" @click="unavailable('Halaman data berikutnya')">
            10
          </button>
<button  aria-label="Halaman Selanjutnya" class="p-2 rounded-lg text-on-surface-variant hover:text-on-surface bg-surface-container-low hover:bg-surface-container transition-colors" type="button" @click="unavailable('Halaman data berikutnya')">
<span class="material-symbols-outlined text-lg">chevron_right</span>
</button>
</div>
</div>
</div>
<div class="mt-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-on-surface-variant">
<div class="font-label-sm text-label-sm text-outline">
        Sinkronisasi terakhir: Hari ini, 15:42 WIB
      </div>
</div>
</div>
</div>
</main>
</x-layouts.bk>

