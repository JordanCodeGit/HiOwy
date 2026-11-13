<x-layouts.bk title="Detail Laporan" page="report" mode="dashboard">
@php
    $urgencyLabel = match(strtolower($report->urgency ?: '')) {
        'darurat', 'urgent', 'tinggi' => 'Darurat',
        'sedang', 'medium' => 'Sedang',
        default => 'Rendah'
    };
    $urgencyBadgeStyle = match(strtolower($report->urgency ?: '')) {
        'darurat', 'urgent', 'tinggi' => 'bg-rose-50 text-rose-700 ring-rose-200 border-rose-200',
        'sedang', 'medium' => 'bg-amber-50 text-amber-700 ring-amber-200 border-amber-200',
        default => 'bg-emerald-50 text-emerald-700 ring-emerald-200 border-emerald-200'
    };
    $urgencyDotColor = match(strtolower($report->urgency ?: '')) {
        'darurat', 'urgent', 'tinggi' => 'bg-rose-500',
        'sedang', 'medium' => 'bg-amber-500',
        default => 'bg-emerald-500'
    };
    $caseStatusNormalized = match($report->status) {
        'belum_dibaca', 'unread' => 'unread',
        'selesai', 'resolved' => 'resolved',
        'sedang_diproses', 'in_progress' => 'in_progress',
        default => 'read'
    };
    $roleIcon = match(strtolower($report->reporter_role ?: '')) {
        'guru' => 'school',
        'orang tua', 'orang tua-wali', 'ortu' => 'family_restroom',
        'teman korban', 'saksi' => 'group',
        default => 'person'
    };
@endphp
<main x-data="bkReport({
    reportId: {{ $report->id }},
    urgency: '{{ $urgencyLabel }}',
    caseStatus: '{{ $caseStatusNormalized }}',
    caseHandler: '{{ addslashes($report->assigned_counselor ?: ($counselors[0] ?? '')) }}',
    walasClass: '{{ $report->reporter_class ?: '7B' }}'
})" class="p-8 max-w-[1580px] w-full mx-auto source-space-y-6">
<div class="flex flex-col gap-6 w-full">
<nav class="flex items-center justify-between text-xs text-slate-500">
<div class="flex items-center gap-2">
<a class="inline-flex items-center gap-1 font-medium text-slate-600 hover:text-slate-900 transition-colors" href="{{ route('bk.dashboard') }}">
<span class="material-symbols-outlined text-[16px]">arrow_back</span>Semua Laporan</a>
<span class="text-slate-300">/</span>
<span class="font-semibold text-slate-900">Detail Kasus #{{ $report->ticket_code }}</span>
</div>
<div class="flex items-center gap-2 text-slate-400">
<span class="material-symbols-outlined text-[16px]">schedule</span>
<span>Diterima: {{ $report->created_at ? $report->created_at->translatedFormat('d M Y, H:i') : '24 Okt 2024, 10:15' }} WIB</span>
</div>
</nav>
<div class="bk-hover-card flex flex-wrap items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
<div class="flex flex-wrap items-center gap-3.5">
<div class="w-10 h-10 rounded-xl {{ str_contains($urgencyBadgeStyle, 'rose') ? 'bg-rose-100 text-rose-700' : (str_contains($urgencyBadgeStyle, 'amber') ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }} flex items-center justify-center">
<span class="material-symbols-outlined text-[24px]">crisis_alert</span>
</div>
<div>
<div class="flex items-center gap-2.5">
<h1 class="text-xl font-bold tracking-tight text-slate-900">#{{ $report->ticket_code }}</h1>
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full {{ $urgencyBadgeStyle }} border text-xs font-bold ring-1">
<span class="w-1.5 h-1.5 rounded-full {{ $urgencyDotColor }}">
</span>{{ $urgencyLabel }}</span>
@if($report->status === 'selesai' || $report->status === 'resolved')
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px]">task_alt</span>Selesai</span>
@elseif($report->status === 'sedang_diproses' || $report->status === 'in_progress')
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px]">pending</span>Ditindaklanjuti</span>
@else
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">
<span class="material-symbols-outlined text-[14px]">visibility</span>Dibaca</span>
@endif
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-medium">
<span class="material-symbols-outlined text-[14px]">{{ $roleIcon }}</span>{{ $report->reporter_role ?: 'Siswa' }}</span>
</div>
<p class="text-xs text-slate-500 mt-1">Kasus {{ $report->category }} • {{ $report->is_anonymous ? 'Dilaporkan anonim melalui portal siswa' : 'Dilaporkan dengan identitas resmi' }}</p>
</div>
</div>
<div class="flex items-center gap-2.5">
<button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-all shadow-xs" id="openRelayModalBtn" type="button" @click="openForward()">
<span class="material-symbols-outlined text-[17px] text-blue-600">forward_to_inbox</span>Laporkan ke Wali Kelas</button>
<button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-all shadow-xs" id="archiveCaseBtn" type="button" @click="openModal('staff-archive')">
<span class="material-symbols-outlined text-[17px] text-slate-500">archive</span>Arsipkan</button>
</div>
</div>
<div class="bk-hover-card bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[18px] text-blue-600">linear_scale</span>
<span class="text-xs font-bold uppercase tracking-wider text-slate-700">Alur &amp; Progres Penanganan Kasus</span>
</div>
<span class="text-xs text-slate-500 font-medium">Posisi Saat Ini: 
@if($report->status === 'selesai' || $report->status === 'resolved')
<strong class="text-emerald-600">Selesai / Teratasi</strong>
@elseif($report->status === 'sedang_diproses' || $report->status === 'in_progress')
<strong class="text-amber-600">Tindak Lanjut Koordinasi</strong>
@else
<strong class="text-blue-600">Dibaca &amp; Diproses BK</strong>
@endif
</span>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
<div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/60">
<div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0 font-bold text-xs">
<span class="material-symbols-outlined text-[18px]">check</span>
</div>
<div class="min-w-0">
<p class="text-xs font-bold text-slate-900 truncate">1. Laporan Masuk</p>
<p class="text-[11px] text-slate-500">{{ $report->created_at ? $report->created_at->format('d M, H:i') : '24 Okt, 10:15' }} WIB</p>
</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-xl {{ in_array($caseStatusNormalized, ['read', 'in_progress', 'resolved']) ? 'bg-blue-50/60 border border-blue-200' : 'bg-slate-50/70 border border-slate-200/40 opacity-75' }}">
<div class="w-8 h-8 rounded-lg {{ in_array($caseStatusNormalized, ['read', 'in_progress', 'resolved']) ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center shrink-0 font-bold text-xs">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</div>
<div class="min-w-0">
<p class="text-xs font-bold {{ in_array($caseStatusNormalized, ['read', 'in_progress', 'resolved']) ? 'text-blue-700' : 'text-slate-600' }} truncate">2. Dibaca &amp; Aktif</p>
<p class="text-[11px] text-slate-500">{{ $report->updated_at ? $report->updated_at->format('d M, H:i') : '24 Okt, 10:25' }} WIB</p>
</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-xl {{ in_array($caseStatusNormalized, ['in_progress', 'resolved']) ? 'bg-amber-50/60 border border-amber-200' : 'bg-slate-50/70 border border-slate-200/40 opacity-75' }}">
<div class="w-8 h-8 rounded-lg {{ in_array($caseStatusNormalized, ['in_progress', 'resolved']) ? 'bg-amber-600 text-white ring-4 ring-amber-100' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center shrink-0 font-bold text-xs">
<span class="material-symbols-outlined text-[18px]">pending</span>
</div>
<div class="min-w-0">
<p class="text-xs font-semibold {{ in_array($caseStatusNormalized, ['in_progress', 'resolved']) ? 'text-amber-800 font-bold' : 'text-slate-700' }} truncate">3. Tindak Lanjut</p>
<p class="text-[11px] text-slate-400">Proses koordinasi</p>
</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-xl {{ $caseStatusNormalized === 'resolved' ? 'bg-emerald-50/60 border border-emerald-200' : 'bg-slate-50/70 border border-slate-200/40 opacity-60' }}">
<div class="w-8 h-8 rounded-lg {{ $caseStatusNormalized === 'resolved' ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-slate-200 text-slate-400' }} flex items-center justify-center shrink-0 font-bold text-xs">
<span class="material-symbols-outlined text-[18px]">task_alt</span>
</div>
<div class="min-w-0">
<p class="text-xs font-semibold {{ $caseStatusNormalized === 'resolved' ? 'text-emerald-800 font-bold' : 'text-slate-600' }} truncate">4. Selesai / Arsip</p>
<p class="text-[11px] text-slate-400">{{ $caseStatusNormalized === 'resolved' ? 'Tuntas' : 'Menunggu hasil' }}</p>
</div>
</div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
<div class="lg:col-span-7 flex flex-col gap-6">
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-5">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-blue-600 text-[20px]">description</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Informasi Insiden</h2>
</div>
<span class="text-xs text-slate-400 font-medium">ID: {{ $report->ticket_code }}</span>
</div>
<div class="grid grid-cols-2 gap-3">
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Kejadian</span>
<span class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $report->category }}</span>
</div>
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Kejadian</span>
<span class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $report->incident_time ?: ($report->created_at ? $report->created_at->translatedFormat('d M Y (H:i WIB)') : '-') }}</span>
</div>
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi</span>
<span class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $report->location ?: 'Area Sekolah' }}</span>
</div>
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pihak Terlibat / Peran</span>
<span class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $report->reporter_role ?: 'Siswa' }} {{ $report->reporter_class ? '('.$report->reporter_class.')' : '' }}</span>
</div>
</div>
<div>
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Deskripsi Peristiwa</span>
<div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs leading-relaxed text-slate-800">
<p>“{{ $report->story }}”</p>
</div>
</div>
<div>
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Lampiran Bukti (AES-256)</span>
@if($report->attachment_path)
<div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
<div class="flex items-center gap-3 min-w-0">
<div class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-blue-600 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[20px]">image</span>
</div>
<div class="min-w-0">
<p class="text-xs font-semibold text-slate-800 truncate">{{ basename($report->attachment_path) }}</p>
<p class="text-[11px] text-slate-400">Berkas Lampiran • Terenkripsi</p>
</div>
</div>
<a href="{{ asset($report->attachment_path) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg transition-colors shadow-xs">
<span class="material-symbols-outlined text-[15px]">visibility</span>Buka</a>
</div>
@else
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-400 italic">
Tidak ada berkas bukti lampiran yang disertakan pada laporan ini.
</div>
@endif
</div>
</section>
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[20px] text-blue-600">shield_person</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Identitas Pelapor</h2>
</div>
<span class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">Terkunci Konselor</span>
</div>
@if($report->is_anonymous)
<div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">visibility_off</span>
</div>
<div>
<span class="text-xs font-bold text-slate-900 block">Pelapor Memilih Anonim</span>
<p class="text-[11px] text-slate-500 mt-0.5">Identitas dirahasiakan oleh sistem demi perlindungan dan kenyamanan pelapor.</p>
</div>
</div>
@else
<div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-wrap items-center justify-between gap-4">
<div>
<span class="text-[10px] uppercase font-bold text-slate-400 block">Nama Siswa / Pelapor</span>
<span class="text-xs font-bold text-slate-900 block mt-0.5">{{ $report->reporter_name ?: 'Siswa' }}</span>
<span class="text-[11px] text-slate-500 block">Kelas {{ $report->reporter_class ?: '-' }} • Kontak: {{ $report->reporter_contact ?: 'Tidak ada' }}</span>
</div>
@if($report->reporter_contact)
@php
    $rawPhone = preg_replace('/[^0-9]/', '', $report->reporter_contact);
    $waNumber = str_starts_with($rawPhone, '0') ? '62' . substr($rawPhone, 1) : $rawPhone;
@endphp
<a class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors" href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer">
<span class="material-symbols-outlined text-[16px]">chat</span>Hubungi Siswa (WA)</a>
@endif
</div>
@endif
</section>
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-5">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-blue-600 text-[20px]">forum</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Komunikasi Asinkron Aman</h2>
</div>
<span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Token Terenkripsi</span>
</div>
<div class="flex flex-col gap-3.5">
<div class="flex flex-col items-start gap-1 max-w-[85%]">
<div class="flex items-center gap-2 text-[11px]">
<span class="font-bold text-slate-800">Pelapor ({{ $report->reporter_role ?: 'Siswa' }})</span>
<span class="text-slate-400">{{ $report->created_at ? $report->created_at->format('H:i') : '10:15' }} WIB</span>
</div>
<div class="p-3 rounded-2xl rounded-tl-sm bg-slate-100 text-xs text-slate-900 leading-relaxed">
{{ Str::limit($report->story, 260) }}
</div>
</div>
@if($report->counselor_reply)
<div class="flex flex-col items-end gap-1 self-end max-w-[85%]">
<div class="flex items-center gap-2 text-[11px]">
<span class="text-slate-400">{{ $report->counselor_replied_at ? $report->counselor_replied_at->format('H:i') : '10:28' }} WIB</span>
<span class="font-bold text-blue-700">{{ $report->assigned_counselor ?: 'Konselor BK' }}</span>
</div>
<div class="p-3 rounded-2xl rounded-tr-sm bg-blue-600 text-xs text-white leading-relaxed">
{{ $report->counselor_reply }}
</div>
</div>
@endif
</div>
<div class="pt-2 flex flex-col gap-2.5">
<label class="text-xs font-bold text-slate-700" for="counselorReply">Balasan Cepat Konselor</label>
<textarea x-model="counselorReply" class="w-full p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all" id="counselorReply" placeholder="Tulis respons yang menenangkan untuk siswa..." rows="2"></textarea>
<div class="flex items-center justify-between">
<span class="text-[11px] text-slate-400">Balasan dapat dilihat siswa pada menu Lacak Aduan.</span>
<button class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer" type="button" @click="sendReply()" :disabled="loading">
<span class="material-symbols-outlined text-[16px]">send</span>Kirim Balasan</button>
</div>
</div>
</section>
</div>
<div class="lg:col-span-5 flex flex-col gap-6">
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-blue-600 text-[20px]">tune</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Status &amp; Penugasan</h2>
</div>
<span class="w-2 h-2 rounded-full {{ $caseStatusNormalized === 'resolved' ? 'bg-emerald-500' : ($caseStatusNormalized === 'in_progress' ? 'bg-amber-500' : 'bg-blue-600') }}"></span>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-700" for="caseStatus">Status Kasus</label>
<div class="relative">
<select x-model="caseStatus" class="w-full appearance-none p-2.5 pr-8 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white cursor-pointer" id="caseStatus">
<option value="unread">Belum Dibaca</option>
<option value="read">Dibaca (Aktif)</option>
<option value="in_progress">Ditindaklanjuti Tim BK</option>
<option value="resolved">Selesai / Teratasi</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">arrow_drop_down</span>
</div>
</div>
<div class="flex flex-col gap-1.5">
<label class="text-xs font-semibold text-slate-700" for="caseHandler">Konselor Penanggung Jawab</label>
<div class="relative">
<select x-model="caseHandler" class="w-full appearance-none p-2.5 pr-8 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:bg-white cursor-pointer" id="caseHandler">
@foreach($counselors as $counselor)
<option value="{{ $counselor }}">{{ $counselor }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">arrow_drop_down</span>
</div>
</div>
<div class="flex flex-col gap-1.5">
<span class="text-xs font-semibold text-slate-700">Tingkat Urgensi</span>
<div class="grid grid-cols-3 gap-2" id="urgencySelector">
<button class="urgency-btn py-2 px-2 rounded-lg bg-slate-50 hover:bg-emerald-50 text-xs font-medium text-slate-600 hover:text-emerald-700 border border-slate-200 transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer" type="button" @click="urgency = 'Rendah'" :aria-pressed="urgency === 'Rendah'" :class="{ 'bk-choice-active': urgency === 'Rendah' }">
<span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
<span>Rendah</span>
</button>
<button class="urgency-btn py-2 px-2 rounded-lg bg-slate-50 hover:bg-amber-50 text-xs font-medium text-slate-600 hover:text-amber-700 border border-slate-200 transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer" type="button" @click="urgency = 'Sedang'" :aria-pressed="urgency === 'Sedang'" :class="{ 'bk-choice-active': urgency === 'Sedang' }">
<span class="w-2 h-2 rounded-full bg-amber-600 shrink-0"></span>
<span>Sedang</span>
</button>
<button class="urgency-btn py-2 px-2 rounded-lg bg-slate-50 hover:bg-rose-100 text-xs font-medium text-slate-600 border border-slate-200 shadow-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer" type="button" @click="urgency = 'Darurat'" :aria-pressed="urgency === 'Darurat'" :class="{ 'bk-choice-active': urgency === 'Darurat' }">
<span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
<span>Darurat</span>
</button>
</div>
</div>
<div class="text-[11px] flex items-center justify-between text-slate-500 px-0.5" id="urgencyStatusNote">
<span>Level terpilih: <strong x-text="urgency" class="text-rose-700 font-semibold" id="urgencyLabelText">{{ $urgencyLabel }}</strong></span>
</div>
<button class="w-full py-2.5 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition-colors shadow-xs text-xs mt-1 cursor-pointer" type="button" @click="updateCase()" :disabled="loading">
<span x-show="!loading">Perbarui Kasus</span>
<span x-show="loading">Menyimpan...</span>
</button>
</section>
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[19px] text-blue-600">lock</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Catatan Rahasia BK</h2>
</div>
<span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Internal</span>
</div>
@if($report->internal_notes)
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-2 max-h-48 overflow-y-auto">
@foreach(explode("\n\n", $report->internal_notes) as $noteItem)
@if(trim($noteItem))
<div class="text-xs text-slate-700 leading-normal border-b border-slate-200/50 pb-1.5 last:border-b-0 last:pb-0">
{{ $noteItem }}
</div>
@endif
@endforeach
</div>
@endif
<div class="flex flex-col gap-2">
<textarea x-model="internalNote" class="w-full p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none transition-all" placeholder="Tambah catatan rahasia antar konselor..." rows="2"></textarea>
<button class="self-end px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition-colors cursor-pointer" type="button" @click="saveInternalNote()" :disabled="loading">Simpan Catatan</button>
</div>
</section>
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[19px] text-blue-600">forward_to_inbox</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">LAPORKAN ke Wali Kelas</h2>
</div>
@if($report->forwarded_to_walas)
<span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Terkirim ke Walas</span>
@else
<span class="text-[11px] font-medium text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Belum Diteruskan</span>
@endif
</div>
@if($report->forwarded_to_walas)
<div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-1.5">
<div class="flex items-center justify-between text-[11px]">
<span class="font-semibold text-blue-700">Wali Kelas {{ $report->reporter_class ?: '7B' }}</span>
<span class="text-slate-400">{{ $report->forwarded_at ? $report->forwarded_at->format('d M, H:i') : '10:30' }} WIB</span>
</div>
<p class="text-xs italic text-slate-700">Ringkasan aman insiden telah diteruskan ke portal wali kelas untuk observasi pendampingan.</p>
<div class="flex items-center gap-1 text-[11px] text-emerald-600">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
<span>Telah terdistribusi ke portal Wali Kelas</span>
</div>
</div>
@endif
<button class="w-full py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors flex items-center justify-center gap-1.5 cursor-pointer" id="quickRelayBtn" type="button" @click="openForward()">
<span class="material-symbols-outlined text-[16px] text-blue-600">add_circle</span>
<span>{{ $report->forwarded_to_walas ? 'Kirim Ringkasan Tambahan' : 'Kirim Ringkasan ke Wali Kelas' }}</span>
</button>
</section>
<section class="bk-hover-card bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col gap-4">
<div class="flex items-center justify-between pb-3 border-b border-slate-100">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[19px] text-blue-600">history</span>
<h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Riwayat Singkat Aktivitas</h2>
</div>
<span class="text-xs text-slate-400">Audit Trail</span>
</div>
<div class="flex flex-col gap-3 pl-4 relative before:absolute before:left-1 before:top-1.5 before:bottom-1.5 before:w-0.5 before:bg-slate-200">
@if($report->forwarded_to_walas)
<div class="relative flex flex-col gap-0.5">
<div class="absolute -left-[19px] top-1 w-2 h-2 rounded-full bg-blue-600 ring-2 ring-white"></div>
<div class="flex items-center justify-between text-xs">
<span class="font-bold text-slate-800">Eskalasi Terkirim</span>
<span class="text-[11px] text-slate-400">{{ $report->forwarded_at ? $report->forwarded_at->format('H:i') : '10:30' }} WIB</span>
</div>
<span class="text-[11px] text-slate-500">Ringkasan aman dikirim ke portal Wali Kelas</span>
</div>
@endif
@if($report->counselor_reply)
<div class="relative flex flex-col gap-0.5">
<div class="absolute -left-[19px] top-1 w-2 h-2 rounded-full bg-blue-600 ring-2 ring-white"></div>
<div class="flex items-center justify-between text-xs">
<span class="font-bold text-slate-800">Balasan Konselor</span>
<span class="text-[11px] text-slate-400">{{ $report->counselor_replied_at ? $report->counselor_replied_at->format('H:i') : '10:28' }} WIB</span>
</div>
<span class="text-[11px] text-slate-500">{{ $report->assigned_counselor ?: 'Konselor BK' }} mengirimkan respons resmi</span>
</div>
@endif
<div class="relative flex flex-col gap-0.5">
<div class="absolute -left-[19px] top-1 w-2 h-2 rounded-full bg-blue-600 ring-2 ring-white"></div>
<div class="flex items-center justify-between text-xs">
<span class="font-bold text-slate-800">Laporan Ditelaah</span>
<span class="text-[11px] text-slate-400">{{ $report->updated_at ? $report->updated_at->format('H:i') : '10:25' }} WIB</span>
</div>
<span class="text-[11px] text-slate-500">Konselor membuka tiket laporan</span>
</div>
<div class="relative flex flex-col gap-0.5">
<div class="absolute -left-[19px] top-1 w-2 h-2 rounded-full bg-slate-400 ring-2 ring-white"></div>
<div class="flex items-center justify-between text-xs">
<span class="font-bold text-slate-800">Laporan Diterima</span>
<span class="text-[11px] text-slate-400">{{ $report->created_at ? $report->created_at->format('H:i') : '10:15' }} WIB</span>
</div>
<span class="text-[11px] text-slate-500">Tiket aduan #{{ $report->ticket_code }} dibuat oleh sistem</span>
</div>
</div>
</section>
<div class="flex items-center justify-between pt-1 text-xs text-slate-500">
<button class="inline-flex items-center gap-1.5 text-slate-500 hover:text-amber-600 transition-colors cursor-pointer" id="markPrankBtn" type="button" @click="openModal('isengModal')">
<span class="material-symbols-outlined text-[16px]">flag</span>Tandai Laporan Iseng</button>
<span class="text-[11px] text-slate-400">Kepatuhan Hukum: Tanpa Hapus</span>
</div>
</div>
</div>
</div>
<template x-teleport="body">
<div role="dialog" aria-modal="true" tabindex="-1" @keydown.tab="trapFocus($event)" @keydown.escape.window="closeModal()" x-cloak x-show="modal === 'isengModal'" x-transition.opacity class="bk-modal bk-dashboard-theme fixed inset-0 z-50 " id="isengModal">
<div @click="closeModal()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" id="modalBackdrop"></div>
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
<button @click="closeModal()" class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer" id="cancelModalBtn" type="button">
Batal
</button>
<button @click="confirmMarkIseng()" class="px-4 py-2 rounded-lg text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white transition-colors shadow-xs cursor-pointer" id="confirmModalBtn" type="button">
Ya, Tandai Iseng
</button>
</div>
</div>
</div>
</div>
</template>
<x-bk.forward-modal :wali-kelas-list="$waliKelasList" />
<div role="dialog" aria-modal="true" tabindex="-1" @keydown.tab="trapFocus($event)" @keydown.escape.window="closeModal()" x-cloak x-show="modal === 'staff-archive'" x-transition.opacity class="bk-modal fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/40 p-4 backdrop-blur-sm" id="staff-archive">
<div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 source-space-y-4">
<div class="flex items-start gap-4">
<div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[24px]">archive</span>
</div>
<div class="source-space-y-1">
<h3 class="text-base font-bold text-slate-900">Arsipkan Laporan Ini?</h3>
<p class="text-xs text-slate-600 leading-relaxed">
Laporan akan dipindahkan dari antrean aktif ke arsip kasus selesai. Riwayat dan audit trail tetap tersimpan.
</p>
</div>
</div>
<div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-100">
<button @click="closeModal()" class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer" type="button">
Batal
</button>
<button @click="confirmArchive()" class="px-4 py-2 rounded-lg text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white transition-colors shadow-xs cursor-pointer" type="button">
Ya, Arsipkan
</button>
</div>
</div>
</div>
</main>
</x-layouts.bk>
