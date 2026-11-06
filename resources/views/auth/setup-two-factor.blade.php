<x-layouts.auth title="Pengaturan 2FA" body-class="bg-surface font-body-md text-body-md text-on-surface min-h-screen" :medium-fonts="false">
    <main class="w-full min-h-screen bg-surface flex flex-col" x-data="{ itModal: false }">
        <div class="flex flex-col w-full">
            <div class="w-full min-h-screen bg-surface flex flex-col lg:flex-row">
                <!-- LEFT COLUMN: 2/5 Solid Deep Navy Institutional Security Anchor -->
                <aside class="w-full lg:w-5/12 bg-primary-container text-inverse-on-surface flex flex-col justify-between relative overflow-hidden shadow-xl p-6 sm:p-8 lg:p-12"><!-- Ambient Glow Behind Mascot -->
                    <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-secondary opacity-20 blur-3xl pointer-events-none"></div>
                    <div class="absolute bottom-1/3 -right-20 w-80 h-80 rounded-full bg-tertiary-fixed opacity-10 blur-3xl pointer-events-none"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 rounded-full bg-secondary opacity-15 blur-3xl pointer-events-none"></div>
                    <!-- Top Branding & Status Header -->
                    <x-auth.panel-header />

                    <!-- Center Mascot Section with Large Image & Warm Welcoming Copy -->
                    <div class="relative z-10 flex flex-col items-center justify-center text-center my-auto py-6 source-space-y-4">
                        <div class="relative group flex w-full flex-col items-center">
                            <x-auth.mascot pose="6" class="object-contain drop-shadow-2xl" />
                            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-inverse-surface/90 text-inverse-on-surface font-label-sm text-label-sm shadow-md backdrop-blur-md -mt-2">
                                <span class="material-symbols-outlined text-secondary-fixed text-base" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                                Pengaturan Keamanan Wajib
                            </div>
                        </div>
                        <div class="source-space-y-2 max-w-sm px-2">
                            <h2 class="font-headline-md text-headline-md text-inverse-on-surface font-semibold tracking-tight">Yuk! Aktifkan Verifikasi 2 Langkah</h2>
                        </div>
                        <div class="w-full max-w-sm source-space-y-2.5 pt-4 text-left">
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-inverse-surface/40 backdrop-blur-xs">
                                <div class="w-6 h-6 rounded-full bg-secondary text-on-secondary flex items-center justify-center font-label-sm text-label-sm shrink-0">1</div>
                                <p class="font-title-md text-title-md text-inverse-on-surface">Buka Aplikasi Autentikator</p></div>
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-secondary/20 backdrop-blur-xs">
                                <div class="w-6 h-6 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-label-sm text-label-sm font-bold shrink-0">2</div>
                                <p class="font-title-md text-title-md text-inverse-on-surface font-semibold">Scan QR &amp; Verifikasi</p></div>
                        </div>
                    </div>
                    <x-auth.version />
                </aside>
                <!-- RIGHT COLUMN: 3/5 Clean Light Multi-Step Setup Panel -->
                <main class="w-full lg:w-7/12 p-6 sm:p-10 lg:p-12 flex flex-col justify-between bg-surface">
                    <form method="POST" action="{{ route('two-factor.setup.confirm') }}" id="setup-2fa-form" class="max-w-2xl mx-auto w-full source-space-y-8" x-data="otpEntry">
                        @csrf
                        <input type="hidden" name="code" x-bind:value="digits.join('')" required>

                        <!-- Header Banner & Instructions -->
                        <div class="source-space-y-2">
                            <div class="flex items-center justify-between"><span class="font-label-sm text-label-sm text-secondary font-bold uppercase tracking-wider">Tahap Konfigurasi</span><span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-on-surface-variant bg-surface-container-high px-2.5 py-1 rounded-full"><span class="material-symbols-outlined text-sm">timer</span> Sesi: 30 Menit</span></div>
                            <h2 class="font-headline-lg text-headline-lg text-on-surface font-semibold tracking-tight">Aktifkan Verifikasi 2 Langkah</h2>
                            <p class="font-body-md text-body-md text-on-surface-variant">Ikuti langkah mudah ini untuk amankan akunmu.</p></div>

                        {{-- Error Alert --}}
                        @if ($errors->any())
                            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 flex items-start gap-3 text-body-sm shadow-xs">
                                <span class="material-symbols-outlined text-title-md shrink-0 mt-0.5">error</span>
                                <div class="flex-1 font-medium">{{ $errors->first() }}</div>
                            </div>
                        @endif

                        <!-- SECTION 1: QR CODE TOTP -->
                        <section class="bg-surface-container-lowest p-6 sm:p-7 rounded-2xl shadow-xs source-space-y-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-label-sm text-label-sm font-bold">1</span>
                                    <h3 class="font-title-lg text-title-lg text-on-surface font-bold">Scan Kode QR</h3>
                            </div></div>
                            <div class="flex flex-col sm:flex-row items-center gap-6 bg-surface-container-low p-5 rounded-xl">
                                <div class="relative bg-surface-container-lowest p-3 rounded-xl shadow-md shrink-0">
                                    @if (!empty($qrCodeUrl))
                                        <img src="{{ $qrCodeUrl }}" alt="QR Code Authenticator" class="w-32 h-32 object-contain select-none" />
                                    @else
                                        <div class="w-32 h-32 flex items-center justify-center text-secondary">
                                            <span class="material-symbols-outlined text-4xl">qr_code</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="source-space-y-3 flex-1 w-full text-center sm:text-left">
                                    <p class="font-body-md text-body-md text-on-surface-variant">Buka Google Authenticator atau aplikasi serupa, lalu scan QR ini.</p>
                                </div>
                            </div>
                        </section>

                        <!-- SECTION 2: VERIFICATION CODE INPUT -->
                        <section class="bg-surface-container-lowest p-6 sm:p-7 rounded-2xl shadow-xs source-space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-label-sm text-label-sm font-bold">2</span>
                                    <h3 class="font-title-lg text-title-lg text-on-surface font-bold">Cek Kode Pertama</h3>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm transition-colors" x-bind:class="digits.join('').length === 6 ? 'bg-tertiary-fixed text-on-tertiary-fixed' : 'bg-surface-container text-on-surface-variant'">
                                    <span class="material-symbols-outlined text-sm font-bold" x-text="digits.join('').length === 6 ? 'check_circle' : 'pending'">check_circle</span>
                                    <span x-text="digits.join('').length === 6 ? 'Terverifikasi' : 'Belum terhubung'">Belum terhubung</span>
                                </span>
                            </div>
                            <p class="font-body-md text-body-md text-on-surface-variant">Ketik 6 digit kode yang muncul di aplikasimu:</p>
                            <div class="flex items-center justify-between max-w-md gap-2 sm:gap-3 py-1">
                                <input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 1" x-on:input="inputDigit($event, 0)" x-on:keydown="moveFocus($event, 0)" x-on:paste.prevent="pasteDigits($event, 0)">
                                <input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 2" x-on:input="inputDigit($event, 1)" x-on:keydown="moveFocus($event, 1)" x-on:paste.prevent="pasteDigits($event, 1)">
                                <input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 3" x-on:input="inputDigit($event, 2)" x-on:keydown="moveFocus($event, 2)" x-on:paste.prevent="pasteDigits($event, 2)">
                                <span class="text-on-surface-variant font-bold text-title-lg">-</span>
                                <input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 4" x-on:input="inputDigit($event, 3)" x-on:keydown="moveFocus($event, 3)" x-on:paste.prevent="pasteDigits($event, 3)">
                                <input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 5" x-on:input="inputDigit($event, 4)" x-on:keydown="moveFocus($event, 4)" x-on:paste.prevent="pasteDigits($event, 4)">
                                <input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 6" x-on:input="inputDigit($event, 5)" x-on:keydown="moveFocus($event, 5)" x-on:paste.prevent="pasteDigits($event, 5)">
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded-lg bg-surface-container-low text-on-secondary-container">
                                <span class="material-symbols-outlined text-secondary text-base">check</span>
                                <span class="font-label-sm text-label-sm" x-text="digits.join('').length === 6 ? 'Kode pas &amp; terhubung' : 'Ketik 6 digit kode dari aplikasi'">Ketik 6 digit kode dari aplikasi</span>
                            </div>
                        </section>

                        <!-- SECURITY POLICIES & PRIMARY CTAs -->
                        <div class="source-space-y-4 pt-2">
                            <div class="flex flex-col sm:flex-row items-center gap-3">
                                <button class="w-full sm:flex-1 py-3.5 px-6 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-title-md text-title-md font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-center cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" type="submit" x-bind:disabled="digits.join('').length < 6">
                                    <span>Selesai &amp; Masuk Dashboard</span>
                                    <span class="material-symbols-outlined text-xl">arrow_forward</span>
                                </button>
                                <button class="w-full sm:w-auto py-3.5 px-5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg transition-colors flex items-center justify-center gap-1.5 cursor-pointer" type="button" x-on:click="itModal = true">
                                    <span class="material-symbols-outlined text-lg">support_agent</span>
                                    <span>Bantuan IT</span>
                                </button>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant text-center pt-1">Sesi otomatis selesai jika 30 menit tidak aktif.</p>
                        </div>
                    </form>
                    <!-- Footer Help Line -->
                    <footer class="mt-8 pt-6 border-none text-center">

                    </footer>
                </main>
            </div>
        </div>

        <!-- MODAL: Bantuan IT BK -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" x-cloak x-show="itModal" x-transition.opacity>
            <div class="w-full max-w-md bg-surface-container-lowest rounded-2xl shadow-2xl p-6 border border-outline-variant/30 text-on-surface relative" x-on:click.outside="itModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-outline-variant/20">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-xl bg-secondary/15 text-secondary flex items-center justify-center">
                            <span class="material-symbols-outlined text-title-md">support_agent</span>
                        </span>
                        <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Layanan Bantuan IT BK</h3>
                    </div>
                    <button class="text-on-surface-variant hover:text-on-surface p-1 rounded-lg cursor-pointer" type="button" x-on:click="itModal = false">
                        <span class="material-symbols-outlined text-title-md">close</span>
                    </button>
                </div>

                <div class="mt-4 space-y-3.5 text-body-sm">
                    <p class="text-on-surface-variant">Jika Anda mengalami kendala saat memindai kode QR atau aplikasi authenticator:</p>
                    <div class="p-3.5 rounded-xl bg-surface-container-low space-y-2">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-secondary text-title-sm">mail</span>
                            <span class="font-semibold">Email:</span>
                            <a href="mailto:it-support@nusantara.sch.id" class="text-secondary hover:underline">it-support@nusantara.sch.id</a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-secondary text-title-sm">chat</span>
                            <span class="font-semibold">WhatsApp:</span>
                            <span class="text-on-surface">+62 812-3456-7890</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-secondary text-title-sm">schedule</span>
                            <span class="font-semibold">Jam Layanan:</span>
                            <span class="text-on-surface">07.00 - 15.30 WIB (Hari Kerja)</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button class="px-5 py-2.5 rounded-xl bg-primary text-on-primary font-semibold text-body-sm hover:bg-surface-tint cursor-pointer transition-all" type="button" x-on:click="itModal = false">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </main>
</x-layouts.auth>
