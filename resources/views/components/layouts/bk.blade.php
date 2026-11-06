@props(['title', 'page', 'mode' => '', 'portal' => 'bk', 'reportCount' => 14, 'notifications' => []])

<!DOCTYPE html>
<html lang="id" class="bg-background">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — HiOwy!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bk-shell min-h-screen bg-background text-on-surface antialiased font-body-md" data-staff-portal="{{ $portal }}" data-staff-page="{{ $page }}" data-staff-login="{{ route('login') }}" x-data="bkShell" @keydown.escape.window="closeOverlays()">
    <a class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:bg-white focus:p-3" href="#bk-content">Lewati navigasi</a>
    <x-bk.sidebar :page="$page" :portal="$portal" :report-count="$reportCount" />
    <div class="min-w-0 lg:pl-64">
        <x-bk.header :portal="$portal" :notifications="$notifications" />
        <div id="bk-content" tabindex="-1" class="bk-content {{ $mode === 'dashboard' ? 'bk-dashboard-theme' : '' }}">
            <div class="bk-preview-label px-4 pt-3 text-[10px] text-slate-500 lg:px-8">Pratinjau frontend · Data contoh desain · Perubahan belum disimpan</div>
            {{ $slot }}
        </div>
    </div>
    <x-bk.mobile-nav :portal="$portal" :page="$page" />
    <x-auth.notice />

    @php
        $needsProfileCompletion = Auth::check() && empty(Auth::user()->school_name);
    @endphp

    @if($needsProfileCompletion)
        <div x-data="{
            open: true,
            photoPreview: null,
            previewPhoto(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.photoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        }" x-cloak x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto" x-transition.opacity>
            <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 flex flex-col gap-5 my-auto" @click.outside.stop="">
                <!-- Header with Icon -->
                <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-secondary/15 text-secondary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">badge</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Lengkapi Profil Staf Konselor</h2>
                        <p class="text-xs text-slate-500">Satu langkah lagi sebelum Anda mulai mendampingi siswa di HiOwy.</p>
                    </div>
                </div>

                <!-- Error Banner -->
                @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 text-xs flex items-start gap-2">
                        <span class="material-symbols-outlined text-base shrink-0 mt-0.5">error</span>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('bk.profile.complete') }}" enctype="multipart/form-data" class="flex flex-col gap-4" @submit="window.onbeforeunload = null;">
                    @csrf

                    <!-- Avatar / Foto Profil Upload -->
                    <div class="flex items-center gap-4 p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/70">
                        <div class="relative shrink-0">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full ring-2 ring-blue-500/30 overflow-hidden bg-slate-100 flex items-center justify-center shadow-inner">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" alt="Preview Foto Profil" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!photoPreview">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <span class="material-symbols-outlined text-3xl sm:text-4xl">account_circle</span>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="$refs.photoInput.click()" class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shadow-md transition-colors cursor-pointer" title="Unggah foto profil">
                                <span class="material-symbols-outlined text-sm">photo_camera</span>
                            </button>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <label class="text-xs font-semibold text-slate-800">
                                Foto Profil <span class="text-[11px] font-normal text-slate-400">(Opsional)</span>
                            </label>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                Format JPG, PNG, atau WEBP. Maks 4MB.
                            </p>
                            <div class="mt-2 flex items-center gap-2">
                                <button type="button" @click="$refs.photoInput.click()" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-slate-900 shadow-2xs transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[15px] text-blue-600">upload</span>
                                    <span x-text="photoPreview ? 'Ganti Foto' : 'Pilih Foto'">Pilih Foto</span>
                                </button>
                                <button type="button" x-show="photoPreview" x-cloak @click="photoPreview = null; $refs.photoInput.value = ''" class="inline-flex items-center text-[11px] text-red-500 hover:text-red-700 hover:underline transition-colors cursor-pointer">
                                    Hapus
                                </button>
                            </div>
                            <input type="file" name="avatar" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden" x-ref="photoInput" @change="previewPhoto($event)">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-700" for="onboarding-fullname">
                            Nama Lengkap &amp; Gelar
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-lg pointer-events-none">badge</span>
                            <input class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" id="onboarding-fullname" name="name" placeholder="misal: Ibu Sarah Jenkins, S.Pd., M.Psi." required value="{{ old('name', Auth::user()->name && !str_contains(Auth::user()->name, '@') ? Auth::user()->name : '') }}">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-700" for="onboarding-schoolname">
                            Asal Sekolah
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-lg pointer-events-none">account_balance</span>
                            <input class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" id="onboarding-schoolname" name="school_name" placeholder="misal: SMP Telkom Purwokerto" required value="{{ old('school_name') }}">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" @click="window.onbeforeunload = null;" class="w-full py-3.5 px-5 rounded-xl bg-[#070e1e] hover:bg-[#131b2e] text-white font-semibold text-sm transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99]">
                            <span>Simpan &amp; Lanjutkan ke Dashboard</span>
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <noscript><p class="fixed bottom-0 inset-x-0 z-50 bg-amber-100 p-3 text-center text-sm">Aktifkan JavaScript untuk interaksi pratinjau.</p></noscript>
</body>
</html>
