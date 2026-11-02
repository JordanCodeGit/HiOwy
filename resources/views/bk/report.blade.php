<x-layouts.bk title="Detail Laporan" page="report" mode="dashboard">
<main x-data="bkReport" class="p-8 max-w-[1580px] w-full mx-auto source-space-y-6">
<div class="flex flex-col gap-6 w-full">
<nav class="flex items-center justify-between text-xs text-slate-500">
<div class="flex items-center gap-2">
<a  class="inline-flex items-center gap-1 font-medium text-slate-600 hover:text-slate-900 transition-colors"  href="{{ route('bk.dashboard') }}" >
<span class="material-symbols-outlined text-[16px]">arrow_back</span>Semua Laporan</a>
<span class="text-slate-300">/</span>
<span class="font-semibold text-slate-900">Detail Kasus #SR-2024-8042</span>
</div>
<div class="flex items-center gap-2 text-slate-400">
<span class="material-symbols-outlined text-[16px]">schedule</span>
<span>Diterima: 24 Okt 2024, 10:15 WIB</span>
</div>
</nav>
<div class="bk-hover-card flex flex-wrap items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
<div class="flex flex-wrap items-center gap-3.5">
<div class="w-10 h-10 rounded-xl bg-error-container text-error flex items-center justify-center">
<span class="material-symbols-outlined text-[24px]">crisis_alert</span>
</div>
<div>
<div class="flex items-center gap-2.5">
<h1 class="text-xl font-bold tracking-tight text-slate-900">#SR-2024-8042</h1>
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-error/20 text-xs font-bold ring-1 ring-rose-200">
<span class="w-1.5 h-1.5 rounded-full bg-rose-500">
</span>Urgent</span>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-surface-container-high text-secondary text-xs font-semibold">
<span class="material-symbols-outlined text-[14px]">visibility</span>Dibaca</span>
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-medium">
<span class="material-symbols-outlined text-[14px]">school</span>Siswa</span>
</div>
<p class="text-xs text-slate-500 mt-1">Kasus Bullying &amp; Intimidasi • Dilaporkan anonim melalui portal siswa</p>
</div>
</div>
<div class="flex items-center gap-2.5">
<button   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-all shadow-xs" id="openRelayModalBtn" type="button" @click="openForward()">
<span class="material-symbols-outlined text-[17px] text-secondary">forward_to_inbox</span>Laporkan ke Wali Kelas</button>
<button  class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-all shadow-xs" id="archiveCaseBtn" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[17px] text-slate-500">archive</span>Arsipkan</button>
</div>
</div>
<div class="bk-hover-card bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[18px] text-secondary">linear_scale</span>
<span class="text-xs font-bold uppercase tracking-wider text-slate-700">Alur &amp; Progres Penanganan Kasus</span>
</div>
<span class="text-xs text-slate-500 font-medium">Posisi Saat Ini: <strong class="text-secondary">Dibaca &amp; Diproses BK</strong>
</span>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
<div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/60">
<div class="w-8 h-8 rounded-lg bg-secondary text-white flex items-center justify-center shrink-0 font-bold text-xs">
<span class="material-symbols-outlined text-[18px]">check</span>
</div>
<div class="min-w-0">
<p class="text-xs font-bold text-slate-900 truncate">1. Laporan Masuk</p>
<p class="text-[11px] text-slate-500">24 Okt, 10:15 WIB</p>
</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-xl bg-blue-50/60 border border-secondary-container">
<div class="w-8 h-8 rounded-lg bg-secondary text-white flex items-center justify-center shrink-0 font-bold text-xs ring-4 ring-blue-100">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</div>
<div class="min-w-0">
<p class="text-xs font-bold text-secondary truncate">2. Dibaca &amp; Aktif</p>
<p class="text-[11px] text-secondary">24 Okt, 10:25 WIB</p>
</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 border border-slate-200/40 opacity-75">
<div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-600 flex items-center justify-center shrink-0 font-bold text-xs">
<span class="material-symbols-outlined text-[18px]">pending</span>
</div>
<div class="min-w-0">
<p class="text-xs font-semibold text-slate-700 truncate">3. Tindak Lanjut</p>
<p class="text-[11px] text-slate-400">Proses koordinasi</p>
</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 border border-slate-200/40 opacity-60">
<div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-400 flex items-center justify-center shrink-0 font-bold text-xs">
<span class="material-symbols-outlined text-[18px]">task_alt</span>
</div>
<div class="min-w-0">
<p class="text-xs font-semibold text-slate-600 truncate">4. Selesai / Arsip</p>
<p class="text-[11px] text-slate-400">Menunggu hasil</p>
</div>
</div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
<div class="lg:col-span-7 flex flex-col gap-6">
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-5">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-secondary text-[20px]">description</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Informasi Insiden</h2>
</div>
<span class="text-xs text-slate-400 font-medium">ID: SR-2024-8042</span>
</div>
<div class="grid grid-cols-2 gap-3">
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Kejadian</span>
<span class="text-xs font-bold text-slate-900 mt-0.5 block">Bullying Fisik &amp; Intimidasi</span>
</div>
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Kejadian</span>
<span class="text-xs font-bold text-slate-900 mt-0.5 block">24 Okt 2024 (10:05 WIB)</span>
</div>
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi</span>
<span class="text-xs font-bold text-slate-900 mt-0.5 block">Lorong Lt. 2 (Dekat Lab Biologi)</span>
</div>
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pihak Terlibat</span>
<span class="text-xs font-bold text-slate-900 mt-0.5 block">2 Siswa 9B &amp; Korban (8A)</span>
</div>
</div>
<div>
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Deskripsi Peristiwa</span>
<div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs leading-relaxed text-slate-800">
<p>“Saat saya mau mengembalikan buku ke perpustakaan lantai 2 dekat lab biologi, saya dihalangi oleh dua anak kelas 9B. Mereka menarik tas saya sampai robek kecil, menumpahkan botol air, dan mengancam supaya saya tidak melapor kalau tidak mau dipukul waktu jam pulang. Ini sudah ketiga kalinya minggu ini.”</p>
</div>
</div>
<div>
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Lampiran Bukti (AES-256)</span>
<div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
<div class="flex items-center gap-3 min-w-0">
<div class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[20px]">image</span>
</div>
<div class="min-w-0">
<p class="text-xs font-semibold text-slate-800 truncate">bukti_tali_tas_robek_241024.png</p>
<p class="text-[11px] text-slate-400">1.4 MB • Terenkripsi</p>
</div>
</div>
<button  class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg transition-colors shadow-xs" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[15px]">visibility</span>Buka</button>
</div>
</div>
</section>
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[20px] text-secondary">shield_person</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Identitas Pelapor</h2>
</div>
<span class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-secondary border border-blue-100">Terkunci Konselor</span>
</div>
<div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-wrap items-center justify-between gap-4">
<div>
<span class="text-[10px] uppercase font-bold text-slate-400 block">Nama Siswa</span>
<span class="text-xs font-bold text-slate-900 block mt-0.5">Rayhan Aditya (NISN: 0092182910)</span>
<span class="text-[11px] text-slate-500 block">Kelas 8A • Wali: Hendra S., M.Pd.</span>
</div>
<a class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-secondary text-white text-xs font-semibold hover:opacity-95 shadow-xs transition-opacity" href="#" @click.prevent="unavailable('Kontak WhatsApp pelapor')" rel="noopener noreferrer" >
<span class="material-symbols-outlined text-[16px]">chat</span>Hubungi Siswa (WA)</a>
</div>
</section>
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-5">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-secondary text-[20px]">forum</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Komunikasi Asinkron Aman</h2>
</div>
<span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Token Terenkripsi</span>
</div>
<div class="flex flex-col gap-3.5">
<div class="flex flex-col items-start gap-1 max-w-[85%]">
<div class="flex items-center gap-2 text-[11px]">
<span class="font-bold text-slate-800">Pelapor (Siswa)</span>
<span class="text-slate-400">10:15 WIB</span>
</div>
<div class="p-3 rounded-2xl rounded-tl-sm bg-slate-100 text-xs text-slate-900 leading-relaxed">Bu Sarah, saya takut nanti waktu pulang sekolah mereka cegat lagi di gerbang belakang. Tolong bantuannya Bu.</div>
</div>
<div class="flex flex-col items-end gap-1 self-end max-w-[85%]">
<div class="flex items-center gap-2 text-[11px]">
<span class="text-slate-400">10:28 WIB</span>
<span class="font-bold text-secondary">Sarah Jenkins, M.Pd. (Konselor)</span>
</div>
<div class="p-3 rounded-2xl rounded-tr-sm bg-secondary text-xs text-white leading-relaxed">Halo Rayhan, laporan sudah kami terima dan mulai ditindaklanjuti secara rahasia. Jangan khawatir, kamu aman. Tim BK akan mendampingi penjemputanmu di pos gerbang saat bel pulang berbunyi.</div>
</div>
</div>
<div class="pt-2 flex flex-col gap-2.5">
<label class="text-xs font-bold text-slate-700" for="counselorReply">Balasan Cepat Konselor</label>
<textarea class="w-full p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all" id="counselorReply" placeholder="Tulis respons yang menenangkan untuk siswa..." rows="2">
</textarea>
<div class="flex items-center justify-between">
<span class="text-[11px] text-slate-400">Tersinkron langsung dengan token pelapor.</span>
<button  class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition-colors" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-[16px]">send</span>Kirim Balasan</button>
</div>
</div>
</section>
</div>
<div class="lg:col-span-5 flex flex-col gap-6">
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-secondary text-[20px]">tune</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Status &amp; Penugasan</h2>
</div>
<span class="w-2 h-2 rounded-full bg-emerald-500">
</span>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-700" for="caseStatus">Status Kasus</label>
<div class="relative">
<select class="w-full appearance-none p-2.5 pr-8 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white" id="caseStatus">
<option value="unread">Belum Dibaca</option>
<option selected="" value="read">Dibaca (Aktif)</option>
<option value="in_progress">Ditindaklanjuti Tim BK</option>
<option value="resolved">Selesai / Teratasi</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">arrow_drop_down</span>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-700" for="caseHandler">Konselor Penanggung Jawab</label>
<div class="relative">
<select class="w-full appearance-none p-2.5 pr-8 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white" id="caseHandler">
<option selected="" value="sarah">Sarah Jenkins, M.Pd. (Konselor Inti)</option>
<option value="budi">Budi Santoso, S.Psi. (Konselor Kelas 7 &amp; 8)</option>
<option value="ratna">Ratna Kusuma, M.Psi. (Psikolog)</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">arrow_drop_down</span>
</div>
</div>
<div class="flex flex-col gap-1.5">
<span class="text-xs font-semibold text-slate-700">Tingkat Urgensi</span>
<div class="grid grid-cols-3 gap-2" id="urgencySelector">
<button   class="urgency-btn py-2 px-2 rounded-lg bg-slate-50 hover:bg-emerald-50 text-xs font-medium text-slate-600 hover:text-emerald-700 border border-slate-200 transition-all shadow-xs flex items-center justify-center gap-1.5" data-urgency="rendah" type="button" @click="urgency = 'Rendah'" :aria-pressed="urgency === 'Rendah'" :class="{ 'bk-choice-active': urgency === 'Rendah' }">
<span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0">
</span>
<span>Rendah</span>
</button>
<button   class="urgency-btn py-2 px-2 rounded-lg bg-slate-50 hover:bg-amber-50 text-xs font-medium text-slate-600 hover:text-amber-700 border border-slate-200 transition-all shadow-xs flex items-center justify-center gap-1.5" data-urgency="sedang" type="button" @click="urgency = 'Sedang'" :aria-pressed="urgency === 'Sedang'" :class="{ 'bk-choice-active': urgency === 'Sedang' }">
<span class="w-2 h-2 rounded-full bg-amber-600 shrink-0">
</span>
<span>Sedang</span>
</button>
<button   class="urgency-btn py-2 px-2 rounded-lg bg-slate-50 hover:bg-rose-100 text-xs font-medium text-slate-600 border border-slate-200 shadow-xs flex items-center justify-center gap-1.5 transition-all " data-urgency="darurat" type="button" @click="urgency = 'Darurat'" :aria-pressed="urgency === 'Darurat'" :class="{ 'bk-choice-active': urgency === 'Darurat' }">
<span class="w-2 h-2 rounded-full bg-rose-500 shrink-0">
</span>
<span>Darurat</span>
</button>
</div>
</div>
<div class="text-[11px] flex items-center justify-between text-slate-500 px-0.5" id="urgencyStatusNote">
<span>Level terpilih: <strong x-text="urgency" class="text-rose-700 font-semibold" id="urgencyLabelText">Darurat (Tindakan Segera)</strong>
</span>
</div>
<button  class="w-full py-2.5 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition-colors shadow-xs text-xs mt-1" type="button" @click="unavailable()">Perbarui Kasus</button>
</section>
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[19px] text-secondary">lock</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Catatan Rahasia BK</h2>
</div>
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Internal</span>
</div>
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-1">
<div class="flex items-center justify-between text-[11px]">
<span class="font-bold text-slate-800">Sarah Jenkins, M.Pd.</span>
<span class="text-slate-400">10:27 WIB</span>
</div>
<p class="text-xs text-slate-700 leading-normal">Koordinasi dengan tim satpam pos gerbang belakang untuk standby jam 14:00 saat pulang.</p>
</div>
<div class="flex flex-col gap-2">
<textarea class="w-full p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none transition-all" placeholder="Tambah catatan rahasia antar konselor..." rows="2">
</textarea>
<button  class="self-end px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition-colors" type="button" @click="unavailable()">Simpan Catatan</button>
</div>
</section>
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[19px] text-secondary">forward_to_inbox</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">LAPORKAN ke Wali Kelas</h2>
</div>
<span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">1 Terkirim</span>
</div>
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-1.5">
<div class="flex items-center justify-between text-[11px]">
<span class="font-semibold text-secondary">Hendra S., M.Pd. (Wali 8A)</span>
<span class="text-slate-400">10:30 WIB</span>
</div>
<p class="text-xs italic text-slate-700">“Terdapat aduan potensi gesekan antar siswa di area lorong lantai 2 dekat lab biologi saat istirahat. Mohon pengawasan piket lorong ditingkatkan.”</p>
<div class="flex items-center gap-1 text-[11px] text-emerald-600">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
<span>Sudah dibaca oleh Wali Kelas</span>
</div>
</div>
<button   class="w-full py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors flex items-center justify-center gap-1.5" id="quickRelayBtn" type="button" @click="openForward()">
<span class="material-symbols-outlined text-[16px] text-secondary">add_circle</span>Kirim Ringkasan Baru</button>
</section>
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[19px] text-secondary">history</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Riwayat Singkat Aktivitas</h2>
</div>
<span class="text-xs text-slate-400">Audit Trail</span>
</div>
<div class="flex flex-col gap-3 pl-4 relative before:absolute before:left-1 before:top-1.5 before:bottom-1.5 before:w-0.5 before:bg-slate-200">
<div class="relative flex flex-col gap-0.5">
<div class="absolute -left-[19px] top-1 w-2 h-2 rounded-full bg-secondary ring-2 ring-white">
</div>
<div class="flex items-center justify-between text-xs">
<span class="font-bold text-slate-800">Eskalasi Terkirim</span>
<span class="text-[11px] text-slate-400">10:30 WIB</span>
</div>
<span class="text-[11px] text-slate-500">Ringkasan aman dikirim ke portal Wali Kelas 8A</span>
</div>
<div class="relative flex flex-col gap-0.5">
<div class="absolute -left-[19px] top-1 w-2 h-2 rounded-full bg-secondary ring-2 ring-white">
</div>
<div class="flex items-center justify-between text-xs">
<span class="font-bold text-slate-800">Balasan Konselor</span>
<span class="text-[11px] text-slate-400">10:28 WIB</span>
</div>
<span class="text-[11px] text-slate-500">Sarah Jenkins, M.Pd. mengirimkan respons penenangan</span>
</div>
<div class="relative flex flex-col gap-0.5">
<div class="absolute -left-[19px] top-1 w-2 h-2 rounded-full bg-slate-400 ring-2 ring-white">
</div>
<div class="flex items-center justify-between text-xs">
<span class="font-bold text-slate-800">Laporan Diterima</span>
<span class="text-[11px] text-slate-400">10:15 WIB</span>
</div>
<span class="text-[11px] text-slate-500">Tiket anonim #SR-2024-8042 dibuat oleh sistem</span>
</div>
</div>
</section>
<div class="flex items-center justify-between pt-1 text-xs text-slate-500">
<button   class="inline-flex items-center gap-1.5 text-slate-500 hover:text-amber-600 transition-colors" id="markPrankBtn" type="button" @click="openModal('isengModal')">
<span class="material-symbols-outlined text-[16px]">flag</span>Tandai Laporan Iseng</button>
<span class="text-[11px] text-slate-400">Kepatuhan Hukum: Tanpa Hapus</span>
</div>
</div>
</div>
</div>
<template x-teleport="body">
<div role="dialog" aria-modal="true" tabindex="-1" @keydown.tab="trapFocus($event)" @keydown.escape.window="closeModal()" x-cloak x-show="modal === 'isengModal'" x-transition.opacity class="bk-modal bk-dashboard-theme fixed inset-0 z-50 " id="isengModal">
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
<x-bk.forward-modal />
</main>
</x-layouts.bk>

