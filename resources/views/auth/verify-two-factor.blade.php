<x-layouts.auth title="Verifikasi 2FA" body-class="bg-surface font-body-md text-body-md text-on-surface min-h-screen" :medium-fonts="false">
    <main class="w-full min-h-screen bg-surface flex flex-col">
        <div class="flex flex-col w-full">
            <div class="w-full min-h-screen grid grid-cols-1 lg:grid-cols-12 bg-surface">
                <!-- Left 2/5 Column (Dark Navy #070d1e / #0b1329) -->
                <div class="lg:col-span-5 bg-primary-container text-on-primary flex flex-col justify-between relative overflow-hidden p-6 sm:p-8 lg:p-12"><!-- Ambient subtle background glow accents -->
                    <div class="absolute -top-32 -left-32 w-96 h-96 bg-secondary-container/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-tertiary-fixed/10 rounded-full blur-3xl pointer-events-none"></div>
                    <!-- Top Branding Section -->
                    <x-auth.panel-header />
                    <!-- Centered Mascot & Hero Content -->
                    <div class="my-auto py-6 relative z-10 flex flex-col items-center justify-center max-w-sm mx-auto text-center">
                        <div class="auth-mascot-stage relative mb-6 flex items-center justify-center">
                            <div class="absolute inset-0 bg-secondary/20 rounded-full blur-3xl transform scale-95"></div>
                            <x-auth.mascot pose="6" class="object-contain relative z-10 drop-shadow-2xl select-none" /></div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-surface-container-high/20 text-surface-bright font-label-md text-label-md mb-3 shadow-xs border border-secondary-fixed/20 backdrop-blur-xs"><span class="material-symbols-outlined text-body-md text-secondary-fixed">verified_user</span>Verifikasi Dua Langkah</div>
                        <h1 class="font-headline-md text-headline-md sm:text-headline-lg font-bold tracking-tight text-surface-bright text-center mb-3">Lindungi Kerahasiaan Data Kasus Siswa.</h1>
                        <div class="flex flex-wrap items-center gap-2 pt-1 justify-center"><span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-container-highest/15 text-surface-bright font-label-sm text-label-sm"><span class="material-symbols-outlined text-body-md text-secondary-fixed">verified_user</span> 2FA Wajib</span><span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-container-highest/15 text-surface-bright font-label-sm text-label-sm"><span class="material-symbols-outlined text-body-md text-secondary-fixed">timer</span> Sesi 30 Menit</span></div>
                    </div><!-- Bottom Security Tag / Copyright -->
                    <x-auth.version />
                </div>
                <!-- Right 3/5 Column (Clean Light Card Area) -->
                <div class="lg:col-span-7 bg-surface flex flex-col justify-between p-6 sm:p-12 lg:p-16">
                    <!-- Top Action Bar -->
                    <div class="w-full flex items-center justify-between mb-8">
                        <a class="inline-flex items-center gap-2 text-on-surface-variant hover:text-on-surface font-label-md text-label-md transition-colors group" href="{{ route('login') }}">
                            <span class="material-symbols-outlined text-body-md transition-transform group-hover:-translate-x-1">arrow_back</span>
                            Kembali ke Login
                        </a>

                    </div>
                    <!-- Main Form Container -->
                    <div class="w-full max-w-xl mx-auto flex-1 flex flex-col justify-center py-2">
                        <!-- Header Banner with Shield & Key -->
                        <div class="flex flex-col items-center text-center gap-3 mb-6">
                            <div class="w-11 h-11 rounded-xl bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-headline-sm" style="font-variation-settings: 'FILL' 1;">lock_open</span></div>
                            <div>
                                <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">Verifikasi 2 Langkah</h2>
                                <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Cek kode 6 digit di aplikasi autentikator kamu.</p></div>
                        </div>
                        <!-- Account Identifier Pill Card -->
                        <div class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-surface-container-low mb-6">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-surface-container-high flex items-center justify-center text-secondary"><span class="material-symbols-outlined text-body-md">person</span></div>
                                <span class="font-title-md text-body-md text-on-surface font-semibold">Akun belum terhubung</span></div>
                            <span class="px-2 py-0.5 rounded-md bg-secondary-fixed-dim text-on-secondary-fixed font-label-sm text-label-sm font-semibold uppercase tracking-wider">Pratinjau</span></div>
                        <x-auth.validation-errors />
                        <!-- TOTP Inputs & Counter Form -->
                        <form class="source-space-y-6" id="totp-form" x-data="otpEntry" method="POST" action="{{ route('frontend.unavailable', ['page' => 'verify-two-factor']) }}" data-page="verify-two-factor" data-next-url="{{ route('bk.dashboard') }}" x-on:submit.prevent="submitPreview($event)">
                            @csrf
                            <div class="source-space-y-4" id="totp-view" x-show="!recoveryOpen">
                                <div class="flex items-center justify-between">
                                    <label class="font-label-lg text-label-lg text-on-surface font-semibold">Kode 6-Digit Otentikasi</label>
                                    <!-- Countdown Timer Badge with SVG Progress -->
                                    <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container text-on-secondary-container font-label-sm text-label-sm">
                                        <svg class="w-4 h-4 transform -rotate-90 text-secondary shrink-0" viewBox="0 0 36 36">
                                            <path class="text-surface-variant stroke-current" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="4"></path>
                                            <path class="stroke-current" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" id="totp-ring" stroke-dasharray="43.333333333333336, 100" stroke-linecap="round" stroke-width="4" x-bind:stroke-dasharray="`${seconds / 30 * 100}, 100`"></path>
                                        </svg>
                                        <span class="">Kode ganti dalam <span class="font-bold" id="timer-sec" x-text="seconds">13</span> dtk</span>
                                    </div>
                                </div>
                                <!-- 6 Separate Input Boxes -->
                                <div class="grid grid-cols-6 gap-2 sm:gap-3.5">
                                    <input autofocus="" class="totp-input w-full h-14 sm:h-16 text-center font-display-lg text-headline-md sm:text-display-lg bg-surface-container-lowest text-on-surface rounded-xl shadow-xs focus:outline-hidden focus:bg-surface-container-high transition-all" inputmode="numeric" maxlength="1" type="text" data-otp="" aria-label="Digit kode 1" x-bind:required="!recoveryOpen" x-bind:disabled="recoveryOpen" autocomplete="one-time-code" x-on:input="inputDigit($event, 0)" x-on:keydown="moveFocus($event, 0)" x-on:paste.prevent="pasteDigits($event, 0)">
                                    <input class="totp-input w-full h-14 sm:h-16 text-center font-display-lg text-headline-md sm:text-display-lg bg-surface-container-lowest text-on-surface rounded-xl shadow-xs focus:outline-hidden focus:bg-surface-container-high transition-all" inputmode="numeric" maxlength="1" type="text" data-otp="" aria-label="Digit kode 2" x-bind:required="!recoveryOpen" x-bind:disabled="recoveryOpen" autocomplete="off" x-on:input="inputDigit($event, 1)" x-on:keydown="moveFocus($event, 1)" x-on:paste.prevent="pasteDigits($event, 1)">
                                    <input class="totp-input w-full h-14 sm:h-16 text-center font-display-lg text-headline-md sm:text-display-lg bg-surface-container-lowest text-on-surface rounded-xl shadow-xs focus:outline-hidden focus:bg-surface-container-high transition-all" inputmode="numeric" maxlength="1" type="text" data-otp="" aria-label="Digit kode 3" x-bind:required="!recoveryOpen" x-bind:disabled="recoveryOpen" autocomplete="off" x-on:input="inputDigit($event, 2)" x-on:keydown="moveFocus($event, 2)" x-on:paste.prevent="pasteDigits($event, 2)">
                                    <input class="totp-input w-full h-14 sm:h-16 text-center font-display-lg text-headline-md sm:text-display-lg bg-surface-container-lowest text-on-surface rounded-xl shadow-xs focus:outline-hidden focus:bg-surface-container-high transition-all" inputmode="numeric" maxlength="1" type="text" data-otp="" aria-label="Digit kode 4" x-bind:required="!recoveryOpen" x-bind:disabled="recoveryOpen" autocomplete="off" x-on:input="inputDigit($event, 3)" x-on:keydown="moveFocus($event, 3)" x-on:paste.prevent="pasteDigits($event, 3)">
                                    <input class="totp-input w-full h-14 sm:h-16 text-center font-display-lg text-headline-md sm:text-display-lg bg-surface-container-lowest text-on-surface rounded-xl shadow-xs focus:outline-hidden focus:bg-surface-container-high transition-all" inputmode="numeric" maxlength="1" type="text" data-otp="" aria-label="Digit kode 5" x-bind:required="!recoveryOpen" x-bind:disabled="recoveryOpen" autocomplete="off" x-on:input="inputDigit($event, 4)" x-on:keydown="moveFocus($event, 4)" x-on:paste.prevent="pasteDigits($event, 4)">
                                    <input class="totp-input w-full h-14 sm:h-16 text-center font-display-lg text-headline-md sm:text-display-lg bg-surface-container-lowest text-on-surface rounded-xl shadow-xs focus:outline-hidden focus:bg-surface-container-high transition-all" inputmode="numeric" maxlength="1" type="text" data-otp="" aria-label="Digit kode 6" x-bind:required="!recoveryOpen" x-bind:disabled="recoveryOpen" autocomplete="off" x-on:input="inputDigit($event, 5)" x-on:keydown="moveFocus($event, 5)" x-on:paste.prevent="pasteDigits($event, 5)">
                                </div>
                            </div>
                            <input type="hidden" name="code" x-bind:value="digits.join('')" x-bind:disabled="recoveryOpen">
                            <!-- Secondary Alternative Toggle for Recovery Code -->
                            <div class="pt-1">
                                <div class="flex flex-col items-center justify-center pt-1 text-center"><button type="button" x-on:click="toggleRecovery()" x-bind:aria-expanded="recoveryOpen" aria-controls="recovery-box" class="inline-flex items-center gap-1.5 font-body-sm text-body-sm text-secondary hover:text-on-secondary-fixed-variant transition-colors group font-semibold"><span class="material-symbols-outlined text-body-md">vpn_key</span><span id="recovery-btn-text" class="" x-text="recoveryOpen ? 'Gunakan Kode OTP Aplikasi' : 'Gunakan Kode Pemulihan Cadangan (Recovery Code)'">Gunakan Kode Pemulihan Cadangan (Recovery Code)</span></button></div>
                                <div class="pt-3 source-space-y-2" id="recovery-box" x-cloak x-show="recoveryOpen">
                                    <div class="relative"><input class="w-full h-11 px-3.5 font-headline-sm text-headline-sm tracking-widest uppercase bg-surface-container-lowest text-on-surface rounded-xl shadow-xs focus:outline-hidden focus:bg-surface-container-high transition-all" id="recovery-input" maxlength="9" placeholder="ABCD-EF12" type="text" name="recovery_code" x-ref="recoveryInput" autocomplete="off" x-bind:required="recoveryOpen" x-bind:disabled="!recoveryOpen" x-on:input="formatRecovery($event)">
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant"><span class="material-symbols-outlined text-body-md">key</span></div>
                                    </div><p class="font-body-sm text-body-sm text-on-surface-variant text-center">Gunakan kode pemulihan yang diberikan saat mengaktifkan 2FA.</p></div>
                            </div>
                            <!-- Submit Button -->
                            <button class="w-full h-12 sm:h-14 rounded-xl bg-primary text-on-primary font-headline-sm text-headline-sm font-semibold hover:bg-on-surface-variant transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/10 active:scale-[0.99]" type="submit"><span class="">Verifikasi &amp; Masuk</span><span class="material-symbols-outlined text-headline-sm">arrow_forward</span></button>
                        </form>
                        <!-- Emergency Assistance Card -->
                        <div class="mt-6 text-center"><button class="inline-flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" type="button" x-on:click="showUnavailable('Kontak bantuan belum tersedia.')"><span class="material-symbols-outlined text-body-md">headset_mic</span>Butuh bantuan? Hubungi Tim IT Sekolah</button></div>
                    </div>
                    <!-- Session Reminder Footer -->
                    <div class="w-full text-center pt-8 text-on-surface-variant font-body-sm text-body-sm">Sesi keluar otomatis setelah 30 menit tidak aktif&nbsp;</div>
                </div>
            </div>
        </div>
    </main>
    </x-layouts.auth>
