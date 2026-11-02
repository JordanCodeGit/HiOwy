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
                        <!-- Eyebrow Pill -->

                        <!-- Main Title & Welcoming Subtitle -->
                        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight leading-tight text-center">
                            Lupa Kata Sandi?
                        </h1>
                        <p class="mt-2.5 font-body-lg text-body-lg text-on-surface-variant leading-relaxed text-center">
                            Masukkan email sekolah yang terdaftar. Kami akan mengirimkan tautan untuk membuat kata sandi baru secara instan.
                        </p>
                        <!-- Form Elements -->
                        <form class="mt-8 source-space-y-6" id="recoveryForm" method="POST" action="{{ route('frontend.unavailable', ['page' => 'forgot-password']) }}" data-page="forgot-password" data-next-url="" x-on:submit.prevent="submitPreview($event)">
                            @csrf
                            <!-- Input Field: School Email -->
                            <div class="source-space-y-2">
                                <label class="block font-label-lg text-label-lg text-on-surface" for="emailInput">
                                    Email</label>
                                <div class="relative flex items-center">
                                    <span class="material-symbols-outlined absolute left-4 text-outline pointer-events-none text-xl">alternate_email</span>
                                    <input class="w-full pl-12 pr-4 py-3.5 bg-surface-container-lowest text-on-surface placeholder:text-outline-variant font-body-md text-body-md rounded-xl shadow-xs focus:outline-hidden focus:bg-surface-container-lowest transition-all duration-200" id="emailInput" placeholder="konselor.sarah@nusantara.sch.id" required="" type="email" name="email" autocomplete="email">
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
                        <!-- Recovery feedback is shown by the shared notice component. -->
                        
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </main>
    </x-layouts.auth>
