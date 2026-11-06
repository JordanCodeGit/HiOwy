<x-layouts.auth title="Lupa Kata Sandi" body-class="bg-surface font-body-md text-on-surface antialiased min-h-screen" :medium-fonts="true">
    <main class="w-full min-h-screen bg-surface">
        <div class="flex flex-col w-full">
            <div class="w-full min-h-screen flex flex-col lg:flex-row">
                <!-- LEFT PANEL: 2/5 width (Dark Navy Hero & Mascot Column) -->
                <div class="w-full lg:w-2/5 min-h-[520px] lg:min-h-screen bg-primary-container text-on-primary flex flex-col justify-between relative overflow-hidden select-none p-6 sm:p-8 lg:p-12">
                    <!-- Ambient Glow Behind Mascot -->
                    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 rounded-full bg-secondary-container/15 blur-3xl pointer-events-none"></div>
                    <div class="absolute bottom-10 left-10 w-56 h-56 rounded-full bg-tertiary-fixed/10 blur-2xl pointer-events-none"></div>
                    <!-- Top Header Brand & Trust Seal -->
                    <x-auth.panel-header />
                    <!-- Center Feature Area: 3D Mascot & Assurance -->
                    <div class="relative z-10 my-auto py-8 lg:py-10 flex flex-col items-center text-center">
                        <!-- Floating Mascot with Gentle CSS Keyframe -->
                        <div class="relative group flex w-full flex-col items-center">
                            <div class="auth-mascot-stage relative flex items-center justify-center">
                                <x-auth.mascot pose="6" class="object-contain filter drop-shadow-2xl" />
                            </div>
                            <!-- Ground Shadow Effect -->
                            <div class="w-32 h-3.5 mx-auto rounded-full bg-on-primary-fixed/50 blur-xs scale-90 group-hover:scale-75 transition-all duration-300"></div>
                        </div>
                        <!-- Role Badge -->
                        <div class="mt-4 inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-surface-container-lowest/15 backdrop-blur-md text-on-primary font-label-sm text-label-sm shadow-xs">
                            <span class="material-symbols-outlined text-secondary-fixed text-sm" style="font-variation-settings: 'FILL' 1;">sentiment_satisfied</span>
                            <span class="">Sahabat Pendamping</span>
                        </div>
                        <!-- Headline & Narrative -->
                        <h2 class="mt-5 font-headline-md text-headline-md sm:text-headline-lg font-bold text-on-primary max-w-sm tracking-tight leading-snug">
                            Jangan Khawatir, Akunmu Tetap Terlindungi.
                        </h2>
                        <!-- 3 Feature Chips -->
                        <div class="mt-7 grid grid-cols-3 gap-2.5 w-full max-w-md">
                            <div class="flex flex-col items-center justify-center p-3 rounded-xl bg-surface-container-lowest/10 backdrop-blur-xs transition-colors hover:bg-surface-container-lowest/15">
                                <span class="material-symbols-outlined text-secondary-fixed text-xl mb-1">link</span>
                                <span class="font-label-sm text-label-sm text-on-primary font-medium">Tautan Aman</span>
                            </div>
                            <div class="flex flex-col items-center justify-center p-3 rounded-xl bg-surface-container-lowest/10 backdrop-blur-xs transition-colors hover:bg-surface-container-lowest/15">
                                <span class="material-symbols-outlined text-secondary-fixed text-xl mb-1">timer</span>
                                <span class="font-label-sm text-label-sm text-on-primary font-medium">Reset 15 Menit</span>
                            </div>
                            <div class="flex flex-col items-center justify-center p-3 rounded-xl bg-surface-container-lowest/10 backdrop-blur-xs transition-colors hover:bg-surface-container-lowest/15">
                                <span class="material-symbols-outlined text-secondary-fixed text-xl mb-1">support_agent</span>
                                <span class="font-label-sm text-label-sm text-on-primary font-medium">Bantuan Cepat</span>
                            </div>
                        </div>
                    </div>
                    <!-- Clean Minimal Footer -->
                    <x-auth.version />
                </div>
                <!-- RIGHT PANEL: 3/5 width (Light Interactive Form Surface) -->
                <div class="w-full lg:w-3/5 min-h-screen bg-surface text-on-surface flex flex-col p-6 sm:p-12 lg:p-12 relative">
                    <!-- Top Navigation & Return Link -->
                    <div class="flex w-full shrink-0 items-start justify-between gap-4">
                        <a class="inline-flex items-center gap-2 py-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high/60 transition-all font-label-md text-label-md group" href="{{ route('login') }}">
                            <span class="material-symbols-outlined text-base transition-transform group-hover:-translate-x-1">arrow_back</span>
                            <span class="">Kembali ke Halaman Masuk</span>
                        </a>
                        <div class="flex shrink-0 items-center gap-2 py-2 text-on-surface-variant font-label-sm text-label-sm">
                            <span class="hidden sm:inline">Butuh panduan?</span>
                            <button class="text-secondary font-semibold hover:underline" type="button" x-on:click="showUnavailable('Kontak bantuan belum tersedia.')">Pusat Bantuan</button>
                        </div>
                    </div>
                    <!-- Centered Interactive Recovery Form -->
                    <div class="w-full max-w-xl mx-auto my-auto py-10 sm:py-14">
                        {{-- Error Alert --}}
                        @if ($errors->any())
                            <div class="p-4 mb-6 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 flex items-start gap-3 text-body-sm shadow-xs">
                                <span class="material-symbols-outlined text-title-md shrink-0 mt-0.5">error</span>
                                <div class="flex-1 font-medium">
                                    {{ $errors->first() }}
                                </div>
                            </div>
                        @endif

                        @if (session('status'))
                            <!-- SUCCESS STATE: Link Sent to Email -->
                            <div class="text-center source-space-y-6">
                                <div class="w-16 h-16 mx-auto rounded-2xl bg-secondary/15 text-secondary flex items-center justify-center shadow-inner">
                                    <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">mark_email_read</span>
                                </div>

                                <div class="source-space-y-2">
                                    <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight leading-tight">
                                        Periksa Email Anda
                                    </h1>
                                    <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed max-w-md mx-auto">
                                        Kami telah mengirimkan tautan pemulihan kata sandi ke <strong class="text-on-surface">{{ session('email') }}</strong>.
                                    </p>
                                </div>

                                <!-- Security reminder -->
                                <div class="p-4 rounded-xl bg-surface-container-low flex items-start gap-3.5 text-left max-w-md mx-auto">
                                    <div class="p-2 rounded-lg bg-surface-container-highest text-secondary shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">lock_clock</span>
                                    </div>
                                    <div class="source-space-y-1">
                                        <h4 class="font-label-md text-label-md text-on-surface font-semibold">Validitas Tautan 15 Menit</h4>
                                        <p class="font-body-sm text-body-sm text-on-surface-variant leading-normal">
                                            Buka tautan tersebut sebelum waktu kedaluwarsa habis untuk membuat sandi baru.
                                        </p>
                                    </div>
                                </div>

                                <!-- Primary Action: Buka Gmail -->
                                <div class="flex flex-col gap-3 max-w-md mx-auto pt-2">
                                    <a href="https://mail.google.com" target="_blank" rel="noopener noreferrer" class="w-full py-4 px-6 bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer active:scale-[0.99]">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                            <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                                        </svg>
                                        <span>Buka Gmail &rarr;</span>
                                    </a>

                                    @if (session('reset_url'))
                                        <!-- Pengujian Lokal Helper -->
                                        <div class="p-3 rounded-xl bg-surface-container-lowest border border-outline-variant/30 text-left">
                                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                                <span class="font-label-sm text-label-sm font-semibold text-secondary flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-sm">developer_mode</span> Mode Pengujian Lokal
                                                </span>
                                            </div>
                                            <p class="font-body-xs text-xs text-on-surface-variant mb-2">
                                                Klik langsung tombol di bawah untuk membuka halaman reset tanpa harus cek inbox Gmail:
                                            </p>
                                            <a href="{{ session('reset_url') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-secondary hover:underline break-all">
                                                <span class="material-symbols-outlined text-sm">open_in_new</span> Buka Halaman Reset Kata Sandi Sekarang
                                            </a>
                                        </div>
                                    @endif

                                    <a href="{{ route('password.request') }}" class="text-secondary font-label-md text-label-md hover:underline pt-2">
                                        Kirim ulang ke email lain
                                    </a>
                                </div>
                            </div>
                        @else
                            <!-- FORM STATE: Input Email -->
                            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight leading-tight text-center">
                                Lupa Kata Sandi?
                            </h1>
                            <p class="mt-2.5 font-body-lg text-body-lg text-on-surface-variant leading-relaxed text-center">
                                Masukkan email sekolah yang terdaftar. Kami akan mengirimkan tautan untuk membuat kata sandi baru secara instan.
                            </p>
                            <!-- Form Elements -->
                            <form class="mt-8 source-space-y-6" id="recoveryForm" method="POST" action="{{ route('password.email') }}">
                                @csrf
                                <!-- Input Field: School Email -->
                                <div class="source-space-y-2">
                                    <label class="block font-label-lg text-label-lg text-on-surface" for="emailInput">
                                        Email</label>
                                    <div class="relative flex items-center">
                                        <span class="material-symbols-outlined absolute left-4 text-outline pointer-events-none text-xl">alternate_email</span>
                                        <input class="w-full pl-12 pr-4 py-3.5 bg-surface-container-lowest text-on-surface placeholder:text-outline-variant font-body-md text-body-md rounded-xl shadow-xs focus:outline-hidden focus:bg-surface-container-lowest transition-all duration-200 border border-outline-variant/30 focus:border-secondary" id="emailInput" placeholder="konselor.sarah@nusantara.sch.id" required="" type="email" name="email" value="{{ old('email') }}" autocomplete="email">
                                    </div>
                                </div>
                                <!-- Security Callout Box (Gentle soft blue container) -->
                                <div class="p-4 rounded-xl bg-surface-container-low flex items-start gap-3.5">
                                    <div class="p-2 rounded-lg bg-surface-container-highest text-secondary shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">lock_clock</span>
                                    </div>
                                    <div class="source-space-y-1">
                                        <h4 class="font-label-md text-label-md text-on-surface font-semibold">Validitas Tautan 15 Menit</h4>
                                        <p class="font-body-md text-body-md text-on-surface-variant leading-normal">
                                            Tautan pemulihan hanya berlaku selama 15 menit</p>
                                    </div>
                                </div>
                                <!-- Primary CTA Button -->
                                <button class="w-full py-4 px-6 bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer active:scale-[0.99]" id="submitBtn" type="submit">
                                    <span class="">Kirim Tautan Pemulihan</span>
                                    <span class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </main>
    </x-layouts.auth>
