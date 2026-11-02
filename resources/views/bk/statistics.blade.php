<x-layouts.bk title="Statistik" page="statistics" mode="">
<main x-data="bkStatistics" class="relative  w-full min-h-screen bg-background">
<div class="flex flex-col w-full px-8 py-8 gap-8">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
<div class="flex flex-col gap-1">
<div class="inline-flex items-center gap-2 mb-1">
<span class="w-2 h-2 rounded-full bg-secondary">
</span>
<span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest">Dashboard Konseling Terpadu</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Statistik &amp; Analitik Kasus</h1>
<p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
        Data agregat pelaporan untuk evaluasi program pencegahan sekolah. Menjaga kerahasiaan identitas siswa secara mutlak.
      </p>
</div>
<div class="flex flex-wrap items-center gap-2.5">
<div class="flex items-center bg-surface-container-lowest px-3 py-2 rounded-xl shadow-xs gap-2">
<span class="material-symbols-outlined text-secondary text-lg">school</span>
<select @change="unavailable('Data statistik untuk periode yang dipilih'); $el.value = $el.dataset.initial" x-init="$el.dataset.initial = $el.value" class="bg-transparent font-label-md text-label-md text-on-surface focus:outline-none cursor-pointer">
<option>T.A. 2024/2025</option>
<option>T.A. 2023/2024</option>
</select>
</div>
<div class="flex items-center bg-surface-container-lowest px-3 py-2 rounded-xl shadow-xs gap-2">
<span class="material-symbols-outlined text-secondary text-lg">date_range</span>
<select @change="unavailable('Data statistik untuk periode yang dipilih'); $el.value = $el.dataset.initial" x-init="$el.dataset.initial = $el.value" class="bg-transparent font-label-md text-label-md text-on-surface focus:outline-none cursor-pointer">
<option>Semester Ganjil</option>
<option>Semester Genap</option>
</select>
</div>
<button  class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl font-label-md text-label-md hover:bg-on-surface-variant transition-colors shadow-xs" type="button" @click="unavailable()">
<span class="material-symbols-outlined text-base">download</span>
<span>Ekspor Ringkasan PDF</span>
</button>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Total Laporan Masuk</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">folder_shared</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-3">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">62</span>
<div class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-sm text-label-sm">
<span class="material-symbols-outlined text-xs">trending_down</span>
<span>-14%</span>
</div>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-1">vs Semester Genap lalu</span>
<div class="w-full bg-surface-container-low h-1 rounded-full mt-4 overflow-hidden">
<div class="bg-secondary h-full rounded-full" style="width: 72%;">
</div>
</div>
</div>
<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Tingkat Penyelesaian</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-on-tertiary-container">
<span class="material-symbols-outlined text-xl">verified</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-3">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">92%</span>
<span class="font-label-sm text-label-sm text-on-tertiary-container bg-surface-container-low px-2 py-0.5 rounded-full font-semibold">Optimal</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-1">Rata-rata respon awal: &lt;24 Jam</span>
<div class="w-full bg-surface-container-low h-1 rounded-full mt-4 overflow-hidden">
<div class="bg-on-tertiary-container h-full rounded-full" style="width: 92%;">
</div>
</div>
</div>
<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Tren Bulan Ini</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">pending_actions</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-3">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">8</span>
<span class="font-label-sm text-label-sm text-secondary bg-surface-container-high px-2 py-0.5 rounded-full font-semibold">Kasus Aktif</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-1">4 dalam pendampingan intensif</span>
<div class="w-full bg-surface-container-low h-1 rounded-full mt-4 overflow-hidden">
<div class="bg-secondary-container h-full rounded-full" style="width: 45%;">
</div>
</div>
</div>
<div class="p-5 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant font-semibold">Kategori Dominan</span>
<div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">record_voice_over</span>
</div>
</div>
<div class="mt-4 flex items-baseline gap-2">
<span class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight truncate">Verbal</span>
<span class="font-headline-sm text-headline-sm text-secondary font-bold">38%</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant mt-1">Fokus intervensi edukasi kelas</span>
<div class="w-full bg-surface-container-low h-1 rounded-full mt-4 overflow-hidden">
<div class="bg-secondary h-full rounded-full" style="width: 38%;">
</div>
</div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
<div class="bk-hover-card lg:col-span-7 bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between mb-6">
<div class="flex flex-col gap-0.5">
<h2 class="font-title-lg text-title-lg text-on-surface font-bold">Tren Laporan Bulanan</h2>
<span class="font-body-sm text-body-sm text-on-surface-variant">Semester Ganjil 2024 (Juli - Desember)</span>
</div>
<div class="flex items-center gap-4">
<div class="flex items-center gap-1.5">
<span class="w-2.5 h-2.5 rounded-sm bg-secondary">
</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Selesai</span>
</div>
<div class="flex items-center gap-1.5">
<span class="w-2.5 h-2.5 rounded-sm bg-secondary-fixed-dim">
</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Proses</span>
</div>
</div>
</div>
<div class="w-full flex flex-col justify-end pt-4">
<div class="h-56 w-full flex items-end justify-between gap-3 px-2">
<div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
<span class="font-label-sm text-label-sm text-on-surface opacity-0 group-hover:opacity-100 transition-opacity">14</span>
<div class="w-full max-w-[42px] flex flex-col items-stretch gap-1 rounded-lg overflow-hidden bg-surface-container-low p-1">
<div class="bg-secondary-fixed-dim w-full rounded-sm transition-all" style="height: 18px;">
</div>
<div class="bg-secondary w-full rounded-sm transition-all" style="height: 98px;">
</div>
</div>
<span class="font-label-md text-label-md text-on-surface-variant mt-1">Jul</span>
</div>
<div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
<span class="font-label-sm text-label-sm text-on-surface opacity-0 group-hover:opacity-100 transition-opacity">16</span>
<div class="w-full max-w-[42px] flex flex-col items-stretch gap-1 rounded-lg overflow-hidden bg-surface-container-low p-1">
<div class="bg-secondary-fixed-dim w-full rounded-sm transition-all" style="height: 22px;">
</div>
<div class="bg-secondary w-full rounded-sm transition-all" style="height: 114px;">
</div>
</div>
<span class="font-label-md text-label-md text-on-surface-variant mt-1">Ags</span>
</div>
<div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
<span class="font-label-sm text-label-sm text-on-surface opacity-0 group-hover:opacity-100 transition-opacity">11</span>
<div class="w-full max-w-[42px] flex flex-col items-stretch gap-1 rounded-lg overflow-hidden bg-surface-container-low p-1">
<div class="bg-secondary-fixed-dim w-full rounded-sm transition-all" style="height: 14px;">
</div>
<div class="bg-secondary w-full rounded-sm transition-all" style="height: 78px;">
</div>
</div>
<span class="font-label-md text-label-md text-on-surface-variant mt-1">Sep</span>
</div>
<div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
<span class="font-label-sm text-label-sm text-on-surface opacity-0 group-hover:opacity-100 transition-opacity">9</span>
<div class="w-full max-w-[42px] flex flex-col items-stretch gap-1 rounded-lg overflow-hidden bg-surface-container-low p-1">
<div class="bg-secondary-fixed-dim w-full rounded-sm transition-all" style="height: 10px;">
</div>
<div class="bg-secondary w-full rounded-sm transition-all" style="height: 64px;">
</div>
</div>
<span class="font-label-md text-label-md text-on-surface-variant mt-1">Okt</span>
</div>
<div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
<span class="font-label-sm text-label-sm text-on-surface opacity-0 group-hover:opacity-100 transition-opacity">7</span>
<div class="w-full max-w-[42px] flex flex-col items-stretch gap-1 rounded-lg overflow-hidden bg-surface-container-low p-1">
<div class="bg-secondary-fixed-dim w-full rounded-sm transition-all" style="height: 10px;">
</div>
<div class="bg-secondary w-full rounded-sm transition-all" style="height: 48px;">
</div>
</div>
<span class="font-label-md text-label-md text-on-surface-variant mt-1">Nov</span>
</div>
<div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
<span class="font-label-sm text-label-sm text-on-surface opacity-0 group-hover:opacity-100 transition-opacity">5</span>
<div class="w-full max-w-[42px] flex flex-col items-stretch gap-1 rounded-lg overflow-hidden bg-surface-container-low p-1">
<div class="bg-secondary-fixed-dim w-full rounded-sm transition-all" style="height: 8px;">
</div>
<div class="bg-secondary w-full rounded-sm transition-all" style="height: 32px;">
</div>
</div>
<span class="font-label-md text-label-md text-on-surface-variant mt-1">Des</span>
</div>
</div>
</div>
</div>
<div class="bk-hover-card lg:col-span-5 bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between mb-4">
<div class="flex flex-col gap-0.5">
<h2 class="font-title-lg text-title-lg text-on-surface font-bold">Distribusi Jenis Kejadian</h2>
<span class="font-body-sm text-body-sm text-on-surface-variant">Proporsi bentuk interaksi bermasalah</span>
</div>
<span class="material-symbols-outlined text-outline text-xl">pie_chart</span>
</div>
<div class="flex flex-col sm:flex-row items-center justify-center gap-6 my-auto py-2">
<div class="relative w-40 h-40 flex items-center justify-center shrink-0">
<svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 36 36">
<circle class="stroke-surface-container" cx="18" cy="18" fill="none" r="14" stroke-width="4.5">
</circle>
<circle class="stroke-secondary" cx="18" cy="18" fill="none" r="14" stroke-dasharray="33.43 100" stroke-dashoffset="0" stroke-width="4.5">
</circle>
<circle class="stroke-secondary-container" cx="18" cy="18" fill="none" r="14" stroke-dasharray="22.87 100" stroke-dashoffset="-33.43" stroke-width="4.5">
</circle>
<circle class="stroke-secondary-fixed-dim" cx="18" cy="18" fill="none" r="14" stroke-dasharray="18.47 100" stroke-dashoffset="-56.3" stroke-width="4.5">
</circle>
<circle class="stroke-primary-container" cx="18" cy="18" fill="none" r="14" stroke-dasharray="13.19 100" stroke-dashoffset="-74.77" stroke-width="4.5">
</circle>
</svg>
<div class="absolute inset-0 flex flex-col items-center justify-center text-center">
<span class="font-headline-sm text-headline-sm font-bold text-on-surface leading-none">62</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Kasus</span>
</div>
</div>
<div class="flex flex-col gap-3 w-full">
<div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low">
<div class="flex items-center gap-2.5">
<span class="w-3 h-3 rounded-full bg-secondary">
</span>
<span class="font-label-md text-label-md text-on-surface">Ejekan &amp; Verbal</span>
</div>
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md font-bold text-on-surface">38%</span>
</div>
</div>
<div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low">
<div class="flex items-center gap-2.5">
<span class="w-3 h-3 rounded-full bg-secondary-container">
</span>
<span class="font-label-md text-label-md text-on-surface">Bullying Fisik</span>
</div>
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md font-bold text-on-surface">26%</span>
</div>
</div>
<div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low">
<div class="flex items-center gap-2.5">
<span class="w-3 h-3 rounded-full bg-secondary-fixed-dim">
</span>
<span class="font-label-md text-label-md text-on-surface">Medsos / Chat</span>
</div>
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md font-bold text-on-surface">21%</span>
</div>
</div>
<div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low">
<div class="flex items-center gap-2.5">
<span class="w-3 h-3 rounded-full bg-primary-container">
</span>
<span class="font-label-md text-label-md text-on-surface">Pengucilan Relasional</span>
</div>
<div class="flex items-center gap-2">
<span class="font-label-md text-label-md font-bold text-on-surface">15%</span>
</div>
</div>
</div>
</div>
<div class="pt-3">
</div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between mb-5">
<div class="flex flex-col gap-0.5">
<h2 class="font-title-lg text-title-lg text-on-surface font-bold">Distribusi Status Tindak Lanjut</h2>
<span class="font-body-sm text-body-sm text-on-surface-variant">Progres penanganan perkara oleh tim BK</span>
</div>
<div class="px-3 py-1 rounded-full bg-surface-container-low font-label-sm text-label-sm text-secondary font-semibold border border-surface-container">Total 62 Kasus</div>
</div>
<div class="flex flex-col justify-between gap-4.5 my-auto">
<div class="flex flex-col gap-2 p-3 rounded-xl bg-surface-container-low/40 hover:bg-surface-container-low/70 transition-colors">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2.5">
<span class="w-2.5 h-2.5 rounded-full bg-on-tertiary-container ring-4 ring-on-tertiary-container/10">
</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Selesai &amp; Kesepakatan Damai</span>
</div>
<div class="flex items-center gap-2">
<span class="font-headline-sm text-headline-sm font-bold text-on-surface leading-none">57</span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm font-bold bg-surface-container-high text-on-tertiary-container">92%</span>
</div>
</div>
<div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
<div class="bg-on-tertiary-container h-full rounded-full transition-all" style="width: 92%;">
</div>
</div>
</div>
<div class="flex flex-col gap-2 p-3 rounded-xl bg-surface-container-low/40 hover:bg-surface-container-low/70 transition-colors">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2.5">
<span class="w-2.5 h-2.5 rounded-full bg-secondary ring-4 ring-secondary/10">
</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Sedang Ditindaklanjuti</span>
</div>
<div class="flex items-center gap-2">
<span class="font-headline-sm text-headline-sm font-bold text-on-surface leading-none">8</span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm font-bold bg-surface-container-high text-secondary">13%</span>
</div>
</div>
<div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
<div class="bg-secondary h-full rounded-full transition-all" style="width: 13%;">
</div>
</div>
</div>
<div class="flex flex-col gap-2 p-3 rounded-xl bg-surface-container-low/40 hover:bg-surface-container-low/70 transition-colors">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2.5">
<span class="w-2.5 h-2.5 rounded-full bg-secondary-fixed-dim ring-4 ring-secondary-fixed-dim/20">
</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Telah Dibaca / Penjadwalan</span>
</div>
<div class="flex items-center gap-2">
<span class="font-headline-sm text-headline-sm font-bold text-on-surface leading-none">4</span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm font-bold bg-surface-container-high text-on-secondary-fixed-variant">6%</span>
</div>
</div>
<div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
<div class="bg-secondary-fixed-dim h-full rounded-full transition-all" style="width: 6%;">
</div>
</div>
</div>
<div class="flex flex-col gap-2 p-3 rounded-xl bg-surface-container-low/40 hover:bg-surface-container-low/70 transition-colors">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2.5">
<span class="w-2.5 h-2.5 rounded-full bg-outline ring-4 ring-outline/10">
</span>
<span class="font-label-md text-label-md font-semibold text-on-surface">Iseng / Laporan Palsu (Arsip)</span>
</div>
<div class="flex items-center gap-2">
<span class="font-headline-sm text-headline-sm font-bold text-on-surface leading-none">3</span>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm font-bold bg-surface-container-high text-on-surface-variant">5%</span>
</div>
</div>
<div class="w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
<div class="bg-outline h-full rounded-full transition-all" style="width: 5%;">
</div>
</div>
</div>
</div>
</div>
<div class="bk-hover-card bg-surface-container-lowest rounded-xl p-6 shadow-xs flex flex-col justify-between">
<div class="flex items-center justify-between mb-5">
<div class="flex flex-col gap-0.5">
<h2 class="font-title-lg text-title-lg text-on-surface font-bold">Waktu Terjadinya Peristiwa</h2>
<span class="font-body-sm text-body-sm text-on-surface-variant">Pemetaan rentang waktu insiden dilaporkan</span>
</div>
<span class="material-symbols-outlined text-secondary text-xl">schedule</span>
</div>
<div class="grid grid-cols-2 gap-3.5 my-auto">
<div class="p-4 rounded-xl bg-surface-container-low flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Pagi / Masuk Kelas</span>
<span class="material-symbols-outlined text-secondary text-lg">wb_twilight</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-headline-md text-headline-md font-bold text-on-surface">18%</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">11 laporan</span>
</div>
<div class="w-full bg-surface-container h-1 rounded-full mt-2.5 overflow-hidden">
<div class="bg-secondary h-full rounded-full" style="width: 18%;">
</div>
</div>
</div>
<div class="p-4 rounded-xl bg-surface-container-high flex flex-col justify-between relative overflow-hidden">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface font-semibold">Jam Istirahat</span>
<span class="material-symbols-outlined text-secondary text-lg">wb_sunny</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-headline-md text-headline-md font-bold text-on-surface">54%</span>
<span class="font-label-sm text-label-sm text-secondary font-semibold">33 laporan</span>
</div>
<div class="w-full bg-surface-container h-1 rounded-full mt-2.5 overflow-hidden">
<div class="bg-secondary h-full rounded-full" style="width: 54%;">
</div>
</div>
</div>
<div class="p-4 rounded-xl bg-surface-container-low flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Jam Pulang Sekolah</span>
<span class="material-symbols-outlined text-secondary text-lg">directions_walk</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-headline-md text-headline-md font-bold text-on-surface">22%</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">14 laporan</span>
</div>
<div class="w-full bg-surface-container h-1 rounded-full mt-2.5 overflow-hidden">
<div class="bg-secondary h-full rounded-full" style="width: 22%;">
</div>
</div>
</div>
<div class="p-4 rounded-xl bg-surface-container-low flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Sore / Ekstrakurikuler</span>
<span class="material-symbols-outlined text-secondary text-lg">sports_soccer</span>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-headline-md text-headline-md font-bold text-on-surface">6%</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">4 laporan</span>
</div>
<div class="w-full bg-surface-container h-1 rounded-full mt-2.5 overflow-hidden">
<div class="bg-secondary h-full rounded-full" style="width: 6%;">
</div>
</div>
</div>
</div>
<div class="mt-4 pt-4 flex items-center justify-between bg-surface-container-low px-4 py-2.5 rounded-lg">
<span class="font-body-sm text-body-sm text-on-surface">Rekomendasi Satgas: Tingkatkan patroli guru piket di area kantin saat <strong>Jam Istirahat 1 &amp; 2</strong>.</span>
<button   class="font-label-sm text-label-sm text-secondary font-bold hover:underline shrink-0 ml-2" type="button" @click="goToLocations($el.dataset.url)" data-url="{{ route('bk.manage') }}#lokasi">Detail Lokasi</button>
</div>
</div>
</div>
</div>
</main>
</x-layouts.bk>

