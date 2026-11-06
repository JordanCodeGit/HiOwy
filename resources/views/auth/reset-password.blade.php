<x-layouts.auth title="Reset Kata Sandi" body-class="bg-surface font-body-md text-on-surface antialiased min-h-screen" :medium-fonts="true">
    <main class="w-full min-h-screen bg-surface" x-data="{ showPass: false, showConfirm: false }">
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
                        <div class="relative group flex w-full flex-col items-center">
                            <div class="auth-mascot-stage relative flex items-center justify-center">
                                <x-auth.mascot pose="6" class="object-contain filter drop-shadow-2xl" />
                            </div>
                            <!-- Ground Shadow Effect -->
                            <div class="w-32 h-3.5 mx-auto rounded-full bg-on-primary-fixed/50 blur-xs scale-90 group-hover:scale-75 transition-all duration-300"></div>
                        </div>

                        <!-- Role Badge -->
                        <div class="mt-4 inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-surface-container-lowest/15 backdrop-blur-md text-on-primary font-label-sm text-label-sm shadow-xs">
                            <span class="material-symbols-outlined text-secondary-fixed text-sm" style="font-variation-settings: 'FILL' 1;">security</span>
                            <span>Keamanan Akun Terjaga</span>
                        </div>

                        <!-- Headline & Narrative -->
                        <h2 class="mt-5 font-headline-md text-headline-md sm:text-headline-lg font-bold text-on-primary max-w-sm tracking-tight leading-snug">
                            Amankan Kembali Akun Staf Sekolah Anda.
                        </h2>

                        <!-- 3 Feature Chips -->
                        <div class="mt-7 grid grid-cols-3 gap-2.5 w-full max-w-md">
                            <div class="flex flex-col items-center justify-center p-3 rounded-xl bg-surface-container-lowest/10 backdrop-blur-xs transition-colors hover:bg-surface-container-lowest/15">
                                <span class="material-symbols-outlined text-secondary-fixed text-xl mb-1">lock_reset</span>
                                <span class="font-label-sm text-label-sm text-on-primary font-medium">Sandi Baru</span>
                            </div>
                            <div class="flex flex-col items-center justify-center p-3 rounded-xl bg-surface-container-lowest/10 backdrop-blur-xs transition-colors hover:bg-surface-container-lowest/15">
                                <span class="material-symbols-outlined text-secondary-fixed text-xl mb-1">encrypted</span>
                                <span class="font-label-sm text-label-sm text-on-primary font-medium">Enkripsi Kuat</span>
                            </div>
                            <div class="flex flex-col items-center justify-center p-3 rounded-xl bg-surface-container-lowest/10 backdrop-blur-xs transition-colors hover:bg-surface-container-lowest/15">
                                <span class="material-symbols-outlined text-secondary-fixed text-xl mb-1">verified_user</span>
                                <span class="font-label-sm text-label-sm text-on-primary font-medium">Akses Aman</span>
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
                            <span>Kembali ke Halaman Masuk</span>
                        </a>
                    </div>

                    <!-- Centered Interactive Reset Form -->
                    <div class="w-full max-w-xl mx-auto my-auto py-10 sm:py-14">
                        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight leading-tight text-center">
                            Buat Kata Sandi Baru
                        </h1>
                        <p class="mt-2.5 font-body-lg text-body-lg text-on-surface-variant leading-relaxed text-center">
                            Masukkan kata sandi baru yang kuat untuk mengamankan akun staf sekolah Anda.
                        </p>

                        <!-- Account pill identifier -->
                        <div class="mt-4 flex items-center justify-center">
                            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-surface-container text-on-surface font-label-sm text-label-sm border border-outline-variant/30">
                                <span class="material-symbols-outlined text-secondary text-base">alternate_email</span>
                                <span>Akun: <strong>{{ $email }}</strong></span>
                            </span>
                        </div>

                        {{-- Error Alert --}}
                        @if ($errors->any())
                            <div class="mt-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 flex items-start gap-3 text-body-sm shadow-xs">
                                <span class="material-symbols-outlined text-title-md shrink-0 mt-0.5">error</span>
                                <div class="flex-1 font-medium">
                                    {{ $errors->first() }}
                                </div>
                            </div>
                        @endif

                        <!-- Form Elements -->
                        <form class="mt-8 source-space-y-5" method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">

                            <!-- New Password Input -->
                            <div class="source-space-y-2">
                                <label class="block font-label-lg text-label-lg text-on-surface font-semibold" for="new-password">
                                    Kata Sandi Baru
                                </label>
                                <div class="relative flex items-center">
                                    <span class="material-symbols-outlined absolute left-4 text-outline pointer-events-none text-xl">lock</span>
                                    <input class="w-full pl-12 pr-12 py-3.5 bg-surface-container-lowest text-on-surface placeholder:text-outline-variant font-body-md text-body-md rounded-xl shadow-xs border border-outline-variant/30 focus:outline-hidden focus:border-secondary focus:bg-surface-container-lowest transition-all" id="new-password" placeholder="Minimal 8 karakter aman" required="" type="password" name="password" autocomplete="new-password" x-bind:type="showPass ? 'text' : 'password'">
                                    <button class="absolute right-3.5 p-1 text-outline hover:text-on-surface transition-colors cursor-pointer" type="button" x-on:click="showPass = !showPass" title="Tampilkan / Sembunyikan sandi">
                                        <span class="material-symbols-outlined text-xl" x-text="showPass ? 'visibility_off' : 'visibility'">visibility</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password Input -->
                            <div class="source-space-y-2">
                                <label class="block font-label-lg text-label-lg text-on-surface font-semibold" for="confirm-password">
                                    Konfirmasi Kata Sandi Baru
                                </label>
                                <div class="relative flex items-center">
                                    <span class="material-symbols-outlined absolute left-4 text-outline pointer-events-none text-xl">lock_clock</span>
                                    <input class="w-full pl-12 pr-12 py-3.5 bg-surface-container-lowest text-on-surface placeholder:text-outline-variant font-body-md text-body-md rounded-xl shadow-xs border border-outline-variant/30 focus:outline-hidden focus:border-secondary focus:bg-surface-container-lowest transition-all" id="confirm-password" placeholder="Ketik ulang kata sandi baru" required="" type="password" name="password_confirmation" autocomplete="new-password" x-bind:type="showConfirm ? 'text' : 'password'">
                                    <button class="absolute right-3.5 p-1 text-outline hover:text-on-surface transition-colors cursor-pointer" type="button" x-on:click="showConfirm = !showConfirm" title="Tampilkan / Sembunyikan konfirmasi sandi">
                                        <span class="material-symbols-outlined text-xl" x-text="showConfirm ? 'visibility_off' : 'visibility'">visibility</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Requirements Checklist Box -->
                            <div class="p-3.5 rounded-xl bg-surface-container-low text-on-surface-variant font-body-xs text-xs source-space-y-1">
                                <p class="font-semibold text-on-surface">Ketentuan Kata Sandi:</p>
                                <ul class="list-disc list-inside space-y-0.5 text-on-surface-variant">
                                    <li>Minimal 8 karakter</li>
                                    <li>Mengandung kombinasi huruf besar (A-Z) dan kecil (a-z)</li>
                                    <li>Mengandung minimal 1 angka (0-9) dan 1 simbol (!@#$%)</li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <button class="w-full py-4 px-6 bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm font-semibold rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer active:scale-[0.99] mt-2" type="submit">
                                <span>Simpan Kata Sandi &amp; Masuk</span>
                                <span class="material-symbols-outlined text-xl transition-transform group-hover:translate-x-1">arrow_forward</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layouts.auth>
