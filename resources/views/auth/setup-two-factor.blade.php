<x-layouts.auth title="Pengaturan 2FA" headline="Yuk! Aktifkan Verifikasi 2 Langkah" pose="6" :two-factor="true">
    <div x-data="{ itModal: false }" @keydown.escape.window="itModal = false">
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
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm transition-colors"
                          x-bind:class="{
                              'bg-tertiary-fixed text-on-tertiary-fixed': verifyState === 'valid',
                              'bg-red-100 text-red-700': verifyState === 'invalid',
                              'bg-surface-container text-on-surface-variant': verifyState !== 'valid' && verifyState !== 'invalid'
                          }">
                        <span class="material-symbols-outlined text-sm font-bold"
                              x-text="verifyState === 'valid' ? 'check_circle' : (verifyState === 'invalid' ? 'error' : 'pending')">pending</span>
                        <span x-text="verifyState === 'valid' ? 'Terverifikasi' : (verifyState === 'invalid' ? 'Kode Salah' : (verifyState === 'checking' ? 'Memeriksa...' : 'Belum terhubung'))">Belum terhubung</span>
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
                <div class="flex items-center gap-2 p-2 rounded-lg transition-colors"
                     x-bind:class="verifyState === 'invalid' ? 'bg-red-50 text-red-700' : 'bg-surface-container-low text-on-secondary-container'">
                    <span class="material-symbols-outlined text-base"
                          x-bind:class="verifyState === 'invalid' ? 'text-red-600' : 'text-secondary'"
                          x-text="verifyState === 'valid' ? 'check_circle' : (verifyState === 'invalid' ? 'error' : 'info')">info</span>
                    <span class="font-label-sm text-label-sm"
                          x-text="verifyState === 'valid' ? 'Kode pas &amp; terverifikasi' : (verifyState === 'invalid' ? 'Kode salah atau kedaluwarsa. Periksa aplikasi autentikator Anda.' : 'Ketik 6 digit kode dari aplikasi')">Ketik 6 digit kode dari aplikasi</span>
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
        <!-- MODAL: Bantuan IT BK -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" x-cloak x-show="itModal" role="dialog" aria-label="Bantuan IT BK" aria-modal="true" x-transition.opacity>
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
    </div>
</x-layouts.auth>
