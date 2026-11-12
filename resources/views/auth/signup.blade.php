<x-layouts.auth title="Daftar Akun" body-class="bg-surface font-body-md text-on-surface antialiased min-h-screen" :medium-fonts="true">
    <main class="w-full min-h-screen bg-surface">
        <div class="flex flex-col w-full">
            <div class="w-full min-h-screen flex flex-col lg:flex-row">
                <!-- LEFT COLUMN (2/5 Desktop): Dark Midnight Blue / Empathetic Branding & Mascot -->
                <div class="w-full lg:w-2/5 bg-[#070e1e] text-surface-container-lowest flex flex-col justify-between relative overflow-hidden select-none p-6 sm:p-8 lg:p-12">
                    <!-- Ambient Glow Behind Owl -->
                    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-secondary/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute bottom-10 right-0 w-64 h-64 bg-tertiary-fixed-dim/10 rounded-full blur-2xl pointer-events-none"></div>
                    <!-- Top Branding -->
                    <x-auth.panel-header />
                    <!-- Center Feature Block: Friendly 3D Owl Companion -->
                    <div class="relative z-10 my-auto py-10 flex flex-col items-center text-center"><!-- Floating Mascot Container -->
                        <div class="relative mb-6 group flex w-full flex-col items-center">
                            <div class="auth-mascot-stage relative flex items-center justify-center">
                                <x-auth.mascot pose="1" class="object-contain filter drop-shadow-[0_20px_35px_rgba(0,140,230,0.22)]" />
                            </div>
                            <!-- Mascot Status Pill -->
                            <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-[#131b2e]/90 text-surface-dim font-label-md text-label-md shadow-md backdrop-blur-xs -mt-2">
                                <span class="material-symbols-outlined text-[16px] text-secondary-fixed" style="font-variation-settings: &quot;FILL&quot; 1;">favorite</span>
                                <span class="">Sahabat Pendamping</span>
                            </div>
                        </div>
                        <!-- Headline & Welcoming Narrative -->
                        <h2 class="font-headline-md text-headline-sm lg:text-headline-md text-surface-container-lowest max-w-sm mb-3 tracking-tight">
                            Selamat Datang, Sahabat Konselor!
                        </h2>
                        <!-- 3 Feature Chips -->
                        <div class="grid grid-cols-2 gap-3 w-full max-w-sm">
                            <div class="flex flex-col items-center justify-center p-3 rounded-xl bg-surface-container-highest/10 backdrop-blur-xs text-center"><span class="material-symbols-outlined text-secondary-container mb-1 text-[22px]">verified_user</span><span class="font-label-sm text-label-sm text-surface-dim leading-tight">Verifikasi 2 Langkah</span></div>
                            <div class="flex flex-col items-center justify-center p-3 rounded-xl bg-surface-container-highest/10 backdrop-blur-xs text-center"><span class="material-symbols-outlined text-tertiary-fixed mb-1 text-[22px]">timer</span><span class="font-label-sm text-label-sm text-surface-dim leading-tight">Sesi 1 Jam</span></div>
                    </div></div>
                    <!-- Minimal Bottom System Footer -->
                    <x-auth.version />
                </div>
                <!-- RIGHT COLUMN (3/5 Desktop): Clean, Empathetic Form Space -->
                <div class="w-full lg:w-3/5 bg-surface flex flex-col justify-between p-6 sm:p-10 lg:p-14 overflow-y-auto">
                    <!-- Top Utility Nav -->
                    <div class="flex justify-end items-center mb-6">
                        <div class="flex items-center gap-2 font-body-md text-body-md text-on-surface-variant">
                            <span class="">Sudah punya akun resmi?</span>
                            <a class="font-title-md text-secondary hover:text-on-secondary-fixed-variant transition-colors flex items-center gap-1 group" href="{{ route('login') }}"><span class="underline underline-offset-4 decoration-secondary/30 group-hover:decoration-secondary">Masuk Sekarang</span>
                                <span class="material-symbols-outlined text-title-md transition-transform group-hover:translate-x-0.5">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                    <!-- Centered Main Form Container -->
                    <div class="w-full max-w-xl mx-auto flex flex-col justify-center">
                        <!-- Header & Badging -->
                        <div class="mb-7 text-center flex flex-col items-center">
                            <h1 class="font-headline-lg text-headline-lg text-on-surface mb-2 tracking-tight">
                                Daftar Akun Baru
                            </h1>
                            <p class="font-body-md text-body-md text-on-surface-variant">
                                Lengkapi data diri dan institusi untuk mulai mendampingi dan mengelola aduan siswa dengan aman.
                            </p>
                        </div>

                        {{-- Error alert --}}
                        @if ($errors->any())
                            <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 flex items-start gap-3 text-body-sm shadow-xs">
                                <span class="material-symbols-outlined text-title-md shrink-0 mt-0.5">error</span>
                                <div class="flex-1 font-medium">{{ $errors->first() }}</div>
                            </div>
                        @endif

                        <!-- Registration Form -->
                        <form class="source-space-y-4" method="POST" action="{{ route('register.attempt') }}">
                            @csrf
                            <!-- Email Instansi -->
                            <div class="flex flex-col gap-1.5">
                                <label class="font-title-md text-label-lg text-on-surface" for="email">Email</label>
                                <div class="relative flex items-center">
                                    <span class="material-symbols-outlined absolute left-3.5 text-outline text-title-lg pointer-events-none">alternate_email</span>
                                    <input class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-xl font-body-md text-body-md text-on-surface placeholder:text-outline-variant shadow-xs focus:outline-hidden focus:shadow-[0_0_0_3px_rgba(0,99,152,0.18)] transition-all" placeholder="nama@sekolah.sch.id" type="email" id="email" name="email" autocomplete="email" required="" value="{{ old('email', request('email')) }}">
                                </div>
                            </div>
                            <!-- Password Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Buat Kata Sandi -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="font-title-md text-label-lg text-on-surface" for="pass-input">Buat Kata Sandi</label>
                                    <div class="relative flex items-center" x-data="{ visible: false }">
                                        <span class="material-symbols-outlined absolute left-3.5 text-outline text-title-lg pointer-events-none">lock</span>
                                        <input class="w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-xl font-body-md text-body-md text-on-surface placeholder:text-outline-variant shadow-xs focus:outline-hidden focus:shadow-[0_0_0_3px_rgba(0,99,152,0.18)] transition-all" id="pass-input" placeholder="••••••••••••" name="password" autocomplete="new-password" x-bind:type="visible ? 'text' : 'password'" required="" minlength="8">
                                        <button class="absolute right-3.5 text-outline hover:text-on-surface transition-colors" type="button" x-on:click="visible = !visible" x-bind:aria-label="visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" x-bind:aria-pressed="visible">
                                            <span class="material-symbols-outlined text-title-lg" x-text="visible ? 'visibility_off' : 'visibility'">visibility</span>
                                        </button>
                                    </div>
                                </div>
                                <!-- Konfirmasi Kata Sandi -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="font-title-md text-label-lg text-on-surface" for="confirm-pass-input">Konfirmasi Sandi</label>
                                    <div class="relative flex items-center" x-data="{ visible: false }">
                                        <span class="material-symbols-outlined absolute left-3.5 text-outline text-title-lg pointer-events-none">verified_user</span>
                                        <input class="w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-xl font-body-md text-body-md text-on-surface placeholder:text-outline-variant shadow-xs focus:outline-hidden focus:shadow-[0_0_0_3px_rgba(0,99,152,0.18)] transition-all" id="confirm-pass-input" placeholder="••••••••••••" name="password_confirmation" autocomplete="new-password" x-bind:type="visible ? 'text' : 'password'" required="">
                                        <button class="absolute right-3.5 text-outline hover:text-on-surface transition-colors" type="button" x-on:click="visible = !visible" x-bind:aria-label="visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" x-bind:aria-pressed="visible">
                                            <span class="material-symbols-outlined text-title-lg" x-text="visible ? 'visibility_off' : 'visibility'">visibility</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Password Helper Hint -->
                            <div class="flex items-center gap-2 px-1">
                                <span class="material-symbols-outlined text-[16px] text-secondary">info</span>
                                <span class="font-body-sm text-label-sm text-outline">Minimal 8 karakter kombinasi huruf besar, kecil, angka &amp; simbol.</span>
                            </div>
                            <!-- Terms Checkbox -->
                            <div class="pt-2">
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <input {{ old('terms') ? 'checked' : 'checked' }} class="mt-1 w-4 h-4 rounded-sm text-secondary focus:ring-0 focus:ring-offset-0 transition cursor-pointer" type="checkbox" name="terms" required value="1">
                                    <span class="font-body-md text-body-sm text-on-surface-variant leading-relaxed">
                                        Saya menyetujui <span class="text-secondary font-title-md">Ketentuan Layanan</span> dan <span class="text-secondary font-title-md">Kebijakan Privasi Perlindungan Anak</span> dalam penanganan laporan siswa secara rahasia.
                                    </span>
                                </label>
                            </div>
                            <!-- Primary Submit Button -->
                            <div class="pt-3">
                                <button class="w-full h-12 bg-[#070e1e] hover:bg-[#131b2e] text-surface-container-lowest font-title-md text-title-md rounded-xl shadow-md hover:shadow-lg flex items-center justify-center gap-2 transition-all transform active:scale-[0.99]" type="submit">
                                    <span class="">Daftar Akun &amp; Lanjut Verifikasi</span>
                                    <span class="material-symbols-outlined text-title-lg">arrow_forward</span>
                                </button>
                            </div>
                        </form>
                        <!-- Separator -->
                        <div class="relative my-6 flex items-center justify-center">
                            <div class="w-full h-[1px] bg-surface-container-highest"></div>
                            <span class="absolute bg-surface px-4 font-label-md text-label-sm text-outline tracking-wider uppercase">ATAU</span>
                        </div>
                        <!-- Google OAuth Option -->
                        <a href="{{ route('auth.google') }}" class="w-full h-12 bg-surface-container-lowest hover:bg-surface-container-low text-on-surface font-title-md text-body-lg rounded-xl shadow-xs flex items-center justify-center gap-3 transition-all border border-outline-variant/30">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z" fill="#4285F4"></path>
                                <path d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z" fill="#34A853"></path>
                                <path d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.97 0 12s.45 3.82 1.25 5.42l4.03-3.15z" fill="#FBBC05"></path>
                                <path d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z" fill="#EA4335"></path>
                            </svg>
                            <span>Daftar dengan Google</span>
                        </a>
                        <!-- Reassurance Footer Tip -->
                        <div class="mt-8 text-center">
                            <p class="font-body-sm text-body-sm text-outline">
                                Perlu bantuan pendaftaran dinas?
                                <a href="mailto:it-support@nusantara.sch.id" class="text-secondary font-title-md hover:underline inline-flex items-center gap-0.5 ml-1">
                                    <span class="">Hubungi Bantuan IT Sekolah</span>
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                </a>
                            </p>
                        </div>
                    </div>
                    <!-- Empty spacing anchor for visual balance -->
                    <div class="h-4"></div>
                </div>
            </div>
    </div></main>
    </x-layouts.auth>
