<x-layouts.bk title="Halaman Pelajari" page="learn" mode="">
<main x-data="bkLearn" class="relative  w-full min-h-screen bg-background">
<div class="flex flex-col w-full">
<div class="px-8 py-8 flex flex-col gap-8 max-w-7xl mx-auto w-full">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
<div class="flex flex-col gap-1">
<div class="flex items-center gap-2">
<span class="px-2.5 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm uppercase tracking-wider">Modul Kurikulum BK</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Manajemen Halaman Pelajari</h1>
<p class="font-body-md text-body-md text-on-surface-variant">Kelola materi edukasi pencegahan dan pengumuman resmi BK untuk portal siswa.</p>
</div>
<div class="flex items-center gap-3">
<button  class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-surface-container-high bg-surface-container-lowest hover:bg-surface-container-low text-on-surface font-label-md text-label-md shadow-xs transition-all" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-lg text-secondary">visibility</span>
<span>Lihat Portal Siswa</span>
</button>
<button @click="newAnnouncement()"  class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-label-md text-label-md shadow-xs hover:bg-primary-container transition-all" id="btn-focus-form" type="button">
<span class="material-symbols-outlined text-lg">add_circle</span>
<span>Tambah Pengumuman</span>
</button>
</div>
</div>
<div class="relative overflow-hidden rounded-2xl bg-surface-container-lowest shadow-xs p-6 flex flex-col lg:flex-row items-center justify-between gap-6">
<div class="flex items-center gap-5 z-10">
<div class="w-14 h-14 rounded-2xl bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed-variant shrink-0">
<span class="material-symbols-outlined text-3xl">campaign</span>
</div>
<div class="flex flex-col">
<div class="flex items-center gap-2">
<span class="font-title-lg text-title-lg text-on-surface font-bold">Fokus Kampanye Bulan Ini: Lingkungan Bebas Intimidasi</span>
<span class="px-2 py-0.5 rounded-full bg-secondary text-on-secondary font-label-sm text-label-sm">Aktif</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Semua rilis materi baru akan disinkronkan secara real-time ke aplikasi ponsel &amp; kiosk digital siswa.</p>
</div>
</div>
<div class="flex items-center gap-6 z-10 shrink-0">
<div class="flex flex-col items-end">
<span class="font-label-sm text-label-sm text-on-surface-variant">Total Pembaca Siswa</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">1,428 <span class="font-label-sm text-label-sm text-on-tertiary-container">+14%</span>
</span>
</div>
<div class="h-8 w-px bg-surface-container">
</div>
<div class="flex flex-col items-end">
<span class="font-label-sm text-label-sm text-on-surface-variant">Artikel Terbit</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">12 Modul</span>
</div>
</div>
<div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-surface-container-high/40 blur-2xl pointer-events-none">
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
<div class="lg:col-span-5 flex flex-col gap-4 sticky top-24">
<div class="bk-hover-card rounded-2xl bg-surface-container-lowest shadow-xs p-6 flex flex-col gap-6">
<div class="flex items-center justify-between pb-4">
<div class="flex items-center gap-3">
<span class="w-2.5 h-6 rounded-full bg-secondary inline-block">
</span>
<div class="flex flex-col">
<h2 class="font-title-lg text-title-lg text-on-surface font-bold">Form Materi &amp; Edukasi</h2>
</div>
</div>
<span class="px-2.5 py-1 rounded-full bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm flex items-center gap-1">
<span class="material-symbols-outlined text-xs">edit_document</span> Draft
            </span>
</div>
<form method="POST" action="{{ route('frontend.unavailable', ['page' => 'bk']) }}" @submit.prevent="submitUnavailable($event)" class="flex flex-col gap-5" id="announcement-form">
@csrf

<div class="flex flex-col gap-2">
<label class="font-label-lg text-label-lg text-on-surface flex items-center justify-between" for="post-title">
<span>Judul Pengumuman</span>
</label>
<input x-model="title" required maxlength="160" class="w-full px-4 py-3 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:outline-none focus:bg-surface-container-lowest focus:shadow-md transition-all" id="post-title" placeholder="Misal: Pekan Konseling Ramah Remaja..." type="text" value="Pekan Anti-Perundungan &amp; Ruang Cerita Terbuka BK">
</div>
<div class="flex flex-col gap-2">
<label class="font-label-lg text-label-lg text-on-surface">Target Penerima Materi</label>
<div class="grid grid-cols-3 gap-2" id="audience-selector">
<button   class="px-3 py-2.5 rounded-xl font-label-md text-label-md transition-all flex items-center justify-center gap-1.5 bg-surface-container-low text-on-surface-variant"  data-val="siswa"  type="button" @click="audience = 'siswa'" :aria-pressed="audience === 'siswa'" :class="{ 'bk-audience-active': audience === 'siswa' }">
<span class="material-symbols-outlined text-base">school</span>
<span>Siswa</span>
</button>
<button   class="px-3 py-2.5 rounded-xl font-label-md text-label-md transition-all flex items-center justify-center gap-1.5 bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high"  data-val="orang_tua"  type="button" @click="audience = 'orang_tua'" :aria-pressed="audience === 'orang_tua'" :class="{ 'bk-audience-active': audience === 'orang_tua' }">
<span class="material-symbols-outlined text-base">diversity_1</span>
<span>Orang Tua</span>
</button>
<button   class="px-3 py-2.5 rounded-xl font-label-md text-label-md transition-all flex items-center justify-center gap-1.5 bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high"  data-val="umum"  type="button" @click="audience = 'umum'" :aria-pressed="audience === 'umum'" :class="{ 'bk-audience-active': audience === 'umum' }">
<span class="material-symbols-outlined text-base">public</span>
<span>Umum</span>
</button>
</div>
</div>
<div class="flex flex-col gap-2">
<div class="flex items-center justify-between">
<label class="font-label-lg text-label-lg text-on-surface" for="post-content">Isi Pengumuman / Pesan Edukasi</label>
</div>
<div class="rounded-xl bg-surface-container-low overflow-hidden">
<div class="flex items-center gap-1 px-3 py-1.5 bg-surface-container text-on-surface-variant text-xs">
<span class="font-label-sm text-label-sm font-semibold mr-2 text-outline">Format Teks:</span>
<span class="px-2 py-0.5 rounded bg-surface-container-lowest font-mono text-[10px]">Teks Aman</span>
<span class="px-2 py-0.5 rounded bg-surface-container-lowest font-mono text-[10px]">Pemberitahuan Otomatis</span>
</div>
<textarea x-model="content" required class="w-full p-4 bg-transparent text-on-surface font-body-md text-body-md placeholder:text-outline focus:outline-none resize-none leading-relaxed" id="post-content" placeholder="Ketik uraian informasi, anjuran perlindungan, tautan pelaporan resmi..." rows="5">Konselor BK membuka sesi ruang dengar tanpa pencatatan identitas mulai Senin-Jumat pukul 09.00 - 15.00 WIB. Segala kekhawatiran tentang intimidasi, intimidasi siber, atau tekanan akademik dapat dikonsultasikan secara anonim.</textarea>
</div>
</div>
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
<span x-text="visible ? 'Aktif / Tampil' : 'Disembunyikan'" class="px-2.5 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-bold tracking-tight" id="badge-toggle-status">Aktif / Tampil</span>
<label class="relative inline-flex items-center cursor-pointer">
<input x-model="visible" checked="" class="sr-only peer" id="toggle-visible" type="checkbox">
<div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-surface-container-lowest after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface-container-lowest after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary">
</div>
</label>
</div>
</div>
<div class="p-3.5 rounded-xl bg-surface-container-high/40 flex flex-col gap-1.5">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider flex items-center gap-1">
<span class="material-symbols-outlined text-sm">shield</span> Preview Keamanan Klien
              </span>
<p x-text="content.trim() || '(Pratinjau kosong)'" class="font-body-sm text-body-sm text-on-surface line-clamp-2 italic" id="preview-text">
                "Konselor BK membuka sesi ruang dengar tanpa pencatatan identitas mulai Senin-Jumat pukul 09.00 - 15.00 WIB..."
              </p>
</div>
<div class="flex items-center gap-3 pt-2">
<button   class="flex-1 py-3 px-4 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-semibold shadow-md hover:bg-primary-container transition-all flex items-center justify-center gap-2" type="button" @click="saveAnnouncement()">
<span class="material-symbols-outlined text-lg">save</span>
<span>Simpan Pengumuman</span>
</button>
<button   class="px-5 py-3 rounded-xl bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high font-label-lg text-label-lg transition-all" type="button" @click="resetAnnouncement()">
                Batal
              </button>
</div>
</form>
</div>
</div>
<div class="lg:col-span-7 flex flex-col gap-6">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
<div class="flex items-center gap-2">
<span class="font-title-lg text-title-lg text-on-surface font-bold">Arsip Materi Diterbitkan</span>
<span class="px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm font-semibold">3 Tersedia</span>
</div>
<div class="flex items-center gap-2">
<div class="px-3 py-1.5 rounded-xl bg-surface-container-lowest shadow-xs flex items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-base">filter_list</span>
<span class="font-label-sm text-label-sm">Urutkan: Terbaru</span>
</div>
<div class="px-3 py-1.5 rounded-xl bg-surface-container-lowest shadow-xs flex items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-base">tune</span>
<span class="font-label-sm text-label-sm">Semua Kanal</span>
</div>
</div>
</div>
<div class="flex flex-col gap-4">
<div class="bk-hover-card rounded-2xl bg-surface-container-lowest shadow-xs p-6 flex flex-col gap-4 hover:shadow-md transition-all group bk-learning-card">
<div class="flex items-start justify-between gap-4">
<div class="flex flex-col gap-1.5">
<div class="flex items-center gap-2 flex-wrap">
<span class="px-2.5 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm font-semibold">Siswa &amp; Remaja</span>
<span class="px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-bold flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container inline-block">
</span> Portal Aktif
                  </span>
<span class="font-label-sm text-label-sm text-outline">ID: BK-EDU-081</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold group-hover:text-secondary transition-colors">
                  Pekan Konseling Ramah Remaja: Kenali Batas Bercandaanmu
                </h3>
</div>
<div class="flex items-center gap-2 shrink-0 bg-surface-container-low px-3 py-1.5 rounded-xl">
<span class="font-label-sm text-label-sm text-on-tertiary-container font-semibold">Tampil</span>
<label class="relative inline-flex items-center cursor-pointer">
<input  checked="" class="sr-only peer" type="checkbox" @change="unavailable('Perubahan visibilitas materi'); $el.checked = $el.defaultChecked" aria-label="Ubah visibilitas materi">
<div class="w-9 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-surface-container-lowest after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface-container-lowest after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary">
</div>
</label>
</div>
</div>
<p class="font-body-md text-body-md text-on-surface-variant line-clamp-2">
              Panduan interaktif mengenai batas verbal teasing dan bullying relational. Termasuk panduan langkah melapor bila menyaksikan teman sekelas diisolasi secara sosial di grup pertemanan daring.
            </p>
<div class="flex items-center justify-between pt-3 border-t border-surface-container-low">
<div class="flex items-center gap-4 text-on-surface-variant font-label-sm text-label-sm">
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base text-secondary">update</span>
                  Diperbarui: 24 Okt 2024
                </span>
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">person</span>
                  Sarah Jenkins, M.Pd.
                </span>
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">bar_chart</span>
                  842 Tayangan
                </span>
</div>
<div class="flex items-center gap-2">
<button   class="px-3.5 py-1.5 rounded-xl bg-surface-container-low text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors flex items-center gap-1" type="button" @click="previewAnnouncement($el.closest('.bk-learning-card'))">
<span class="material-symbols-outlined text-base">preview</span>
<span>Pratinjau</span>
</button>
<button   class="px-3.5 py-1.5 rounded-xl bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors flex items-center gap-1" type="button" @click="editAnnouncement($el.closest('.bk-learning-card'))">
<span class="material-symbols-outlined text-base">edit</span>
<span>Ubah</span>
</button>
</div>
</div>
</div>
<div class="bk-hover-card rounded-2xl bg-surface-container-lowest shadow-xs p-6 flex flex-col gap-4 hover:shadow-md transition-all group bk-learning-card">
<div class="flex items-start justify-between gap-4">
<div class="flex flex-col gap-1.5">
<div class="flex items-center gap-2 flex-wrap">
<span class="px-2.5 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm font-semibold">Umum &amp; Satgas TPPK</span>
<span class="px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-bold flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container inline-block">
</span> Portal Aktif
                  </span>
<span class="font-label-sm text-label-sm text-outline">ID: BK-DARURAT-004</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold group-hover:text-secondary transition-colors">
                  Hotline Darurat Tim Satgas TPPK Sekolah 2024/2025
                </h3>
</div>
<div class="flex items-center gap-2 shrink-0 bg-surface-container-low px-3 py-1.5 rounded-xl">
<span class="font-label-sm text-label-sm text-on-tertiary-container font-semibold">Tampil</span>
<label class="relative inline-flex items-center cursor-pointer">
<input  checked="" class="sr-only peer" type="checkbox" @change="unavailable('Perubahan visibilitas materi'); $el.checked = $el.defaultChecked" aria-label="Ubah visibilitas materi">
<div class="w-9 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-surface-container-lowest after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface-container-lowest after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary">
</div>
</label>
</div>
</div>
<p class="font-body-md text-body-md text-on-surface-variant line-clamp-2">
              Daftar kontak saluran siaga 24 jam untuk pencegahan kekerasan di lingkungan satuan pendidikan, unit pendampingan psikososial, dan rujukan ke dinas perlindungan anak kota.
            </p>
<div class="flex items-center justify-between pt-3 border-t border-surface-container-low">
<div class="flex items-center gap-4 text-on-surface-variant font-label-sm text-label-sm">
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base text-secondary">update</span>
                  Diperbarui: 18 Okt 2024
                </span>
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">emergency</span>
                  Prioritas Utama
                </span>
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">bar_chart</span>
                  1,102 Tayangan
                </span>
</div>
<div class="flex items-center gap-2">
<button   class="px-3.5 py-1.5 rounded-xl bg-surface-container-low text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors flex items-center gap-1" type="button" @click="previewAnnouncement($el.closest('.bk-learning-card'))">
<span class="material-symbols-outlined text-base">preview</span>
<span>Pratinjau</span>
</button>
<button   class="px-3.5 py-1.5 rounded-xl bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors flex items-center gap-1" type="button" @click="editAnnouncement($el.closest('.bk-learning-card'))">
<span class="material-symbols-outlined text-base">edit</span>
<span>Ubah</span>
</button>
</div>
</div>
</div>
<div class="bk-hover-card rounded-2xl bg-surface-container-lowest shadow-xs p-6 flex flex-col gap-4 hover:shadow-md transition-all group opacity-85 hover:opacity-100 bk-learning-card">
<div class="flex items-start justify-between gap-4">
<div class="flex flex-col gap-1.5">
<div class="flex items-center gap-2 flex-wrap">
<span class="px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm font-semibold">Siswa</span>
<span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-outline font-label-sm text-label-sm font-semibold flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-outline inline-block">
</span> Disembunyikan
                  </span>
<span class="font-label-sm text-label-sm text-outline">ID: BK-CYB-019</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold group-hover:text-secondary transition-colors">
                  Materi Edukasi: Bijak Bersosial Media Tanpa Cyber-Harassment
                </h3>
</div>
<div class="flex items-center gap-2 shrink-0 bg-surface-container-low px-3 py-1.5 rounded-xl">
<span class="font-label-sm text-label-sm text-outline font-semibold">Nonaktif</span>
<label class="relative inline-flex items-center cursor-pointer">
<input  class="sr-only peer" type="checkbox" @change="unavailable('Perubahan visibilitas materi'); $el.checked = $el.defaultChecked" aria-label="Ubah visibilitas materi">
<div class="w-9 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-surface-container-lowest after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface-container-lowest after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary">
</div>
</label>
</div>
</div>
<p class="font-body-md text-body-md text-on-surface-variant line-clamp-2">
              Modul interaktif identifikasi doxxing, penyebaran materi pribadi tanpa persetujuan, dan kiat menjaga kesehatan mental di ruang obrolan anonim online.
            </p>
<div class="flex items-center justify-between pt-3 border-t border-surface-container-low">
<div class="flex items-center gap-4 text-on-surface-variant font-label-sm text-label-sm">
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">update</span>
                  Diperbarui: 02 Okt 2024
                </span>
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-base">visibility_off</span>
                  Masa Tayang Selesai
                </span>
</div>
<div class="flex items-center gap-2">
<button  class="px-3.5 py-1.5 rounded-xl bg-surface-container-low text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors flex items-center gap-1" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-base">visibility</span>
<span>Lihat Riwayat</span>
</button>
<button   class="px-3.5 py-1.5 rounded-xl bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition-colors flex items-center gap-1" type="button" @click="editAnnouncement($el.closest('.bk-learning-card'))">
<span class="material-symbols-outlined text-base">edit</span>
<span>Ubah</span>
</button>
</div>
</div>
</div>
</div>
<div class="rounded-2xl bg-surface-container-low p-6 flex flex-col sm:flex-row items-center gap-6">
<div class="w-full sm:w-1/3 rounded-xl overflow-hidden shrink-0 shadow-xs">
<img alt="Ruang konseling sekolah" class="w-full h-36 object-cover" data-alt="A friendly high school guidance counselor meeting room in a modern Indonesian high school, warm daylight filtering through large windows, soft blue and sage accents, tidy desk with confidential folders and a digital tablet, promoting reassurance, trust, and psychological safety." src="{{ asset('images/bk/counseling-room.jpg') }}">
</div>
<div class="flex flex-col gap-2">
<span class="font-label-sm text-label-sm text-secondary font-bold uppercase tracking-wider">Etika Penulisan Portal Terbuka</span>
<h4 class="font-title-lg text-title-lg text-on-surface font-bold">Pastikan Bahasa Menenangkan &amp; Tidak Menghakimi</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">
              Materi yang Anda rilis langsung dibaca oleh siswa yang mungkin sedang berada dalam tekanan psikis. Hindari diksi ancaman sanksi dan kedepankan ruang dialog aman serta jaminan kerahasiaan identitas.
            </p>
</div>
</div>
</div>
</div>
</div>
</div>
<x-bk.text-preview />
</main>
</x-layouts.bk>

