<x-layouts.auth title="Masuk" body-class="bg-surface font-body-md text-body-md text-on-surface min-h-screen" :medium-fonts="false">
    <main class="w-full min-h-screen bg-surface flex flex-col">
        <div class="flex flex-col w-full">
            <div class="w-full min-h-screen grid grid-cols-1 lg:grid-cols-12 bg-surface">
                <!-- LEFT 2/5 COLUMN: Dark Navy Immersive Security Panel (5 cols on lg) -->
                <div class="lg:col-span-5 bg-primary-container text-on-primary flex flex-col justify-between relative overflow-hidden p-6 sm:p-8 lg:p-12">
                    <!-- Decorative radial atmospheric glows -->
                    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-secondary opacity-20 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-on-tertiary-container opacity-25 blur-3xl pointer-events-none"></div>
                    <!-- Top Header / Institution Identity -->
                    <x-auth.panel-header />
                    <!-- Center Feature Block: Mascot + Core Purpose Copy -->
                    <div class="relative z-10 my-8 lg:my-auto flex flex-col items-center text-center">
                        <!-- Cute Owl Mascot Container with floating animation -->
                        <div class="relative group mb-8 flex w-full flex-col items-center">
                            <div class="auth-mascot-stage relative flex items-center justify-center">
                                <div class="absolute inset-0 rounded-full bg-secondary/25 blur-2xl animate-pulse"></div>
                                <div class="absolute inset-4 rounded-full bg-tertiary-fixed/15 blur-xl"></div>
                                <x-auth.mascot pose="1" class="object-contain relative z-10 drop-shadow-2xl select-none" /></div>
                            <div class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-on-primary/10 backdrop-blur-md text-primary-fixed font-label-sm text-label-sm -mt-3 relative z-20 shadow-md border border-on-primary/10"><span class="material-symbols-outlined text-label-md text-tertiary-fixed" style="font-variation-settings: 'FILL' 1;">favorite</span><span class="">Sahabat Pendamping</span></div>
                        </div>
                        <h1 class="font-headline-md text-headline-md text-on-primary tracking-tight max-w-md mx-auto">
                            Ruang Aman &amp; Terlindungi. Akses Khusus Konselor &amp; Tim Kesiswaan.
                        </h1>
                        <!-- Feature Security Badges -->
                        <div class="w-full max-w-md mt-8 source-space-y-2.5 text-left">
                            <div class="flex items-center justify-center gap-6 pt-2 text-primary-fixed-dim font-label-sm text-label-sm">
                                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-title-md text-tertiary-fixed">phonelink_lock</span><span class="">2FA Wajib</span></div>
                                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-title-md text-primary-fixed-dim">timer</span><span class="">Sesi 30 Menit</span></div>
                        </div></div>
                    </div>
                    <!-- Bottom System Stamp -->
                    <x-auth.version />
                </div>
                <!-- RIGHT 3/5 COLUMN: Clean White / Light Canvas Form (7 cols on lg) -->
                <div class="lg:col-span-7 bg-surface-container-lowest flex flex-col justify-center px-6 py-10 sm:px-12 lg:px-16 xl:px-24">
                    <div class="w-full max-w-xl mx-auto flex flex-col gap-6">
                        <!-- Header Title Block -->
                        <div class="flex flex-col items-center text-center">
                            <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">
                                MASUK</h2>
                            <p class="font-body-md text-body-md text-on-surface-variant mt-1.5">Masuk menggunakan akun&nbsp; sekolah Anda.</p>
                        </div>
                        <!-- Notification Banner: Session Duration -->

                        <!-- Form Elements -->
                        <form class="flex flex-col gap-5 mt-1" method="POST" action="{{ route('frontend.unavailable', ['page' => 'login']) }}" data-page="login" data-next-url="{{ route('two-factor.challenge') }}" x-on:submit.prevent="submitPreview($event)">
                            @csrf
                            <!-- Email Input -->
                            <div class="flex flex-col gap-1.5">
                                <label class="font-label-md text-label-md text-on-surface font-semibold flex items-center justify-between" for="counselor-email">
                                    <span class="">Email Admin</span>

                                </label>
                                <div class="relative flex items-center">
                                    <span class="material-symbols-outlined absolute left-3.5 text-outline text-title-md pointer-events-none">alternate_email</span>
                                    <input class="w-full pl-11 pr-4 py-3 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-xl outline-hidden shadow-xs focus:shadow-md transition-all placeholder:text-outline-variant" id="counselor-email" placeholder="konselor.sarah@nusantara.sch.id" required="" type="email" name="email" autocomplete="username">
                                </div>
                            </div>
                            <!-- Password Input -->
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="font-label-md text-label-md text-on-surface font-semibold" for="counselor-password">Kata Sandi</label>
                                    <a class="font-label-sm text-label-sm text-secondary hover:underline font-semibold" href="{{ route('password.request') }}">Lupa Kata Sandi?</a>
                                </div>
                                <div class="relative flex items-center" x-data="{ visible: false }">
                                    <span class="material-symbols-outlined absolute left-3.5 text-outline text-title-md pointer-events-none">lock</span>
                                    <input class="w-full pl-11 pr-11 py-3 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-xl outline-hidden shadow-xs focus:shadow-md transition-all placeholder:text-outline-variant" id="counselor-password" placeholder="Masukkan kata sandi aman" required="" type="password" name="password" autocomplete="current-password" x-bind:type="visible ? 'text' : 'password'">
                                    <button class="absolute right-3 p-1.5 text-outline hover:text-on-surface transition-colors rounded-lg" id="toggle-pwd-btn" title="Tampilkan / Sembunyikan sandi" type="button" x-on:click="visible = !visible" x-bind:aria-label="visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" x-bind:aria-pressed="visible">
                                        <span class="material-symbols-outlined text-title-md" id="eye-icon" x-text="visible ? 'visibility_off' : 'visibility'">visibility</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Security Lockout / Failed Attempts Tracking Notice -->

                            <!-- Remember me & 2FA Badge -->
                            <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
                                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                    <input checked="" class="w-4 h-4 rounded-sm text-secondary focus:ring-0 cursor-pointer" type="checkbox" name="remember" value="1">
                                    <span class="font-body-sm text-body-sm text-on-surface">Ingat saya di perangkat ini</span>
                                </label>

                            </div>
                            <!-- Primary Submit Button -->
                            <button class="w-full mt-2 py-3.5 px-6 rounded-xl bg-primary text-on-primary font-headline-sm text-headline-sm hover:bg-surface-tint active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg" id="btn-submit" type="submit">
                                <span class="">Lanjutkan ke Verifikasi 2FA</span>
                                <span class="material-symbols-outlined text-title-md">arrow_forward</span>
                            </button>
                            <!-- Alternative SSO Option -->
                            <div class="relative flex items-center justify-center my-1">
                                <div class="w-full bg-outline-variant h-px opacity-30"></div>
                                <span class="absolute px-3 bg-surface-container-lowest font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                                    Atau</span>
                            </div>
                            <button class="w-full py-3 px-5 rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-title-md text-title-md transition-all flex items-center justify-center gap-3" type="button" x-on:click="showUnavailable('Autentikasi Google belum terintegrasi.')">
                                <svg class="w-5 h-5" viewBox="0 0 24 24">
                                    <path d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.15z" fill="#4285F4"></path>
                                    <path d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z" fill="#34A853"></path>
                                    <path d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.16 0 9.98 0 12c0 2.02.45 3.84 1.25 5.42l4.03-3.15z" fill="#FBBC05"></path>
                                    <path d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z" fill="#EA4335"></path>
                                </svg>
                                <span class="">Lanjutkan dengan Google</span>
                            </button>
                        </form>
                        <p class="text-center font-body-sm text-body-sm text-on-surface-variant">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-secondary hover:underline">Daftar akun</a></p>
                        <!-- Help Support Card -->
                        <div class="mt-2 text-center"><span class="font-body-sm text-body-sm text-on-surface-variant">Kendala akses akun? </span><button class="font-label-sm text-label-sm text-secondary hover:underline font-semibold" type="button" x-on:click="showUnavailable('Kontak bantuan belum tersedia.')">Hubungi Bantuan IT BK</button></div>
                        <!-- Trust Badges & Compliance Bar -->

                    </div>
                </div>
            </div>
        </div>
    </main>
    </x-layouts.auth>
