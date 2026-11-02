<x-layouts.auth title="Pengaturan 2FA" body-class="bg-surface font-body-md text-body-md text-on-surface min-h-screen" :medium-fonts="false">
    <main class="w-full min-h-screen bg-surface flex flex-col">
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
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-inverse-surface/40 backdrop-blur-xs">
                                <div class="w-6 h-6 rounded-full bg-inverse-surface text-secondary-fixed flex items-center justify-center font-label-sm text-label-sm shrink-0">3</div>
                                <p class="font-title-md text-title-md text-inverse-on-surface">Simpan Kode Cadangan</p></div>
                        </div>
                    </div>
                    <x-auth.version />
                </aside>
                <!-- RIGHT COLUMN: 3/5 Clean Light Multi-Step Setup Panel -->
                <main class="w-full lg:w-7/12 p-6 sm:p-10 lg:p-12 flex flex-col justify-between bg-surface">
                    <div class="max-w-2xl mx-auto w-full source-space-y-8">
                        <!-- Header Banner & Instructions -->
                        <div class="source-space-y-2">
                            <div class="flex items-center justify-between"><span class="font-label-sm text-label-sm text-secondary font-bold uppercase tracking-wider">Tahap Konfigurasi</span><span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-on-surface-variant bg-surface-container-high px-2.5 py-1 rounded-full"><span class="material-symbols-outlined text-sm">timer</span> Pratinjau 2FA</span></div>
                            <h2 class="font-headline-lg text-headline-lg text-on-surface font-semibold tracking-tight">Aktifkan Verifikasi 2 Langkah</h2>
                            <p class="font-body-md text-body-md text-on-surface-variant">Ikuti langkah mudah ini untuk amankan akunmu.</p></div>
                        <!-- SECTION 1: QR CODE TOTP -->
                        <section class="bg-surface-container-lowest p-6 sm:p-7 rounded-2xl shadow-xs source-space-y-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-label-sm text-label-sm font-bold">1</span>
                                    <h3 class="font-title-lg text-title-lg text-on-surface font-bold">Scan Kode QR</h3>
                            </div></div>
                            <div class="flex flex-col sm:flex-row items-center gap-6 bg-surface-container-low p-5 rounded-xl">
                                <div class="relative bg-surface-container-lowest p-4 rounded-xl shadow-md shrink-0"><svg aria-label="Ilustrasi QR, bukan kode aktivasi" role="img" class="text-primary" fill="currentColor" height="130" viewBox="0 0 140 140" width="130"><rect fill="currentColor" height="36" rx="4" width="36" x="0" y="0"></rect><rect fill="#ffffff" height="24" rx="2" width="24" x="6" y="6"></rect><rect fill="currentColor" height="12" rx="1" width="12" x="12" y="12"></rect><rect fill="currentColor" height="36" rx="4" width="36" x="104" y="0"></rect><rect fill="#ffffff" height="24" rx="2" width="24" x="110" y="6"></rect><rect fill="currentColor" height="12" rx="1" width="12" x="116" y="12"></rect><rect fill="currentColor" height="36" rx="4" width="36" x="0" y="104"></rect><rect fill="#ffffff" height="24" rx="2" width="24" x="6" y="110"></rect><rect fill="currentColor" height="12" rx="1" width="12" x="12" y="116"></rect><rect height="8" rx="1" width="8" x="44" y="8"></rect><rect height="8" rx="1" width="16" x="60" y="8"></rect><rect height="8" rx="1" width="8" x="84" y="8"></rect><rect height="8" rx="1" width="24" x="44" y="24"></rect><rect height="8" rx="1" width="8" x="76" y="24"></rect><rect height="8" rx="1" width="8" x="92" y="24"></rect><rect height="8" rx="1" width="16" x="8" y="44"></rect><rect height="8" rx="1" width="8" x="32" y="44"></rect><rect height="8" rx="1" width="20" x="48" y="44"></rect><rect height="8" rx="1" width="16" x="76" y="44"></rect><rect height="8" rx="1" width="8" x="100" y="44"></rect><rect height="8" rx="1" width="16" x="116" y="44"></rect><rect height="16" rx="1" width="8" x="8" y="60"></rect><rect height="8" rx="1" width="16" x="24" y="60"></rect><rect height="20" rx="1" width="8" x="52" y="60"></rect><rect height="8" rx="1" width="24" x="68" y="60"></rect><rect height="16" rx="1" width="16" x="100" y="60"></rect><rect height="16" rx="1" width="8" x="124" y="60"></rect><rect height="8" rx="1" width="24" x="16" y="84"></rect><rect height="8" rx="1" width="12" x="48" y="88"></rect><rect height="20" rx="1" width="8" x="68" y="76"></rect><rect height="8" rx="1" width="24" x="84" y="84"></rect><rect height="8" rx="1" width="16" x="116" y="84"></rect><rect height="8" rx="1" width="16" x="44" y="104"></rect><rect height="8" rx="1" width="20" x="68" y="104"></rect><rect height="8" rx="1" width="8" x="96" y="104"></rect><rect height="8" rx="1" width="20" x="112" y="104"></rect><rect height="12" rx="1" width="8" x="44" y="120"></rect><rect height="12" rx="1" width="16" x="60" y="120"></rect><rect height="16" rx="1" width="16" x="84" y="116"></rect><rect height="12" rx="1" width="24" x="108" y="120"></rect></svg></div>
                                <div class="source-space-y-3 flex-1 w-full text-center sm:text-left"><p class="font-body-md text-body-md text-on-surface-variant">Contoh tampilan QR. Pengaturan 2FA belum terintegrasi.</p>
                                    <div class="source-space-y-1.5 pt-1"><p class="font-label-sm text-label-sm text-on-surface-variant font-medium">Atau salin kunci manual:</p>
                                        <div class="flex items-center gap-2 bg-surface-container-lowest p-2 rounded-lg shadow-xs"><code class="font-mono text-body-sm font-bold text-secondary tracking-wider truncate flex-1 px-1">BELUM-TERSEDIA</code><button class="px-3 py-1.5 bg-surface-container-high hover:bg-surface-container text-on-surface font-label-sm text-label-sm rounded-md transition-colors flex items-center gap-1 shrink-0" x-on:click="showUnavailable('Kunci 2FA belum tersedia.')" type="button"><span class="material-symbols-outlined text-sm">content_copy</span> Salin Kunci</button></div>
                                </div></div>
                        </div></section>
                        <!-- SECTION 2: VERIFICATION CODE INPUT -->
                        <section class="bg-surface-container-lowest p-6 sm:p-7 rounded-2xl shadow-xs source-space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-label-sm text-label-sm font-bold">2</span>
                                    <h3 class="font-title-lg text-title-lg text-on-surface font-bold">Cek Kode Pertama</h3>
                                </div><span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm"><span class="material-symbols-outlined text-sm font-bold">check_circle</span> Belum terhubung</span></div>
                            <p class="font-body-md text-body-md text-on-surface-variant">Ketik 6 digit kode yang muncul di aplikasimu:</p>
                            <div class="flex items-center justify-between max-w-md gap-2 sm:gap-3 py-1" x-data="otpEntry"><input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 1" x-on:input="inputDigit($event, 0)" x-on:keydown="moveFocus($event, 0)" x-on:paste.prevent="pasteDigits($event, 0)"><input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 2" x-on:input="inputDigit($event, 1)" x-on:keydown="moveFocus($event, 1)" x-on:paste.prevent="pasteDigits($event, 1)"><input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 3" x-on:input="inputDigit($event, 2)" x-on:keydown="moveFocus($event, 2)" x-on:paste.prevent="pasteDigits($event, 2)"><span class="text-on-surface-variant font-bold text-title-lg">-</span><input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 4" x-on:input="inputDigit($event, 3)" x-on:keydown="moveFocus($event, 3)" x-on:paste.prevent="pasteDigits($event, 3)"><input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 5" x-on:input="inputDigit($event, 4)" x-on:keydown="moveFocus($event, 4)" x-on:paste.prevent="pasteDigits($event, 4)"><input class="w-12 h-14 text-center font-display-lg text-headline-lg font-bold bg-surface-container-low text-on-surface rounded-xl shadow-[inset_0_2px_4px_0_rgb(0_0_0/0.05)] focus:outline-hidden" maxlength="1" type="text" inputmode="numeric" autocomplete="off" placeholder="•" data-otp="" aria-label="Digit kode 6" x-on:input="inputDigit($event, 5)" x-on:keydown="moveFocus($event, 5)" x-on:paste.prevent="pasteDigits($event, 5)"></div>
                            <div class="flex items-center gap-2 p-2 rounded-lg bg-surface-container-low text-on-secondary-container"><span class="material-symbols-outlined text-secondary text-base">check</span><span class="font-label-sm text-label-sm">Kode belum diverifikasi oleh server</span></div>
                        </section>
                        <!-- SECTION 3: RECOVERY CODES -->
                        <section class="bg-surface-container-lowest p-6 sm:p-7 rounded-2xl shadow-xs source-space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-label-sm text-label-sm font-bold">3</span>
                                    <h3 class="font-title-lg text-title-lg text-on-surface font-bold">Simpan Kode Cadangan</h3>
                                </div><span class="font-label-sm text-label-sm text-on-surface-variant bg-surface-container px-2 py-0.5 rounded-md">Belum tersedia</span></div>
                            <div class="p-3 rounded-xl bg-surface-container-low text-on-surface-variant flex items-center gap-2.5"><span class="material-symbols-outlined text-secondary text-lg shrink-0">info</span><p class="font-body-sm text-body-sm leading-snug">Kode cadangan akan tersedia setelah 2FA diaktifkan oleh server.</p></div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-1">
                                <div class="bg-surface-container-low p-2.5 rounded-lg text-center font-mono font-bold text-title-md text-on-surface shadow-xs">---- ----</div>
                                <div class="bg-surface-container-low p-2.5 rounded-lg text-center font-mono font-bold text-title-md text-on-surface shadow-xs">---- ----</div>
                                <div class="bg-surface-container-low p-2.5 rounded-lg text-center font-mono font-bold text-title-md text-on-surface shadow-xs">---- ----</div>
                                <div class="bg-surface-container-low p-2.5 rounded-lg text-center font-mono font-bold text-title-md text-on-surface shadow-xs">---- ----</div>
                                <div class="bg-surface-container-low p-2.5 rounded-lg text-center font-mono font-bold text-title-md text-on-surface shadow-xs">---- ----</div>
                                <div class="bg-surface-container-low p-2.5 rounded-lg text-center font-mono font-bold text-title-md text-on-surface shadow-xs">---- ----</div>
                                <div class="bg-surface-container-low p-2.5 rounded-lg text-center font-mono font-bold text-title-md text-on-surface shadow-xs">---- ----</div>
                                <div class="bg-surface-container-low p-2.5 rounded-lg text-center font-mono font-bold text-title-md text-on-surface shadow-xs">---- ----</div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 pt-1"><button class="px-4 py-2 bg-surface-container text-on-surface hover:bg-surface-container-high rounded-xl font-label-md text-label-md flex items-center gap-1.5 transition-colors shadow-xs" x-on:click="showUnavailable('Kode cadangan belum tersedia.')" type="button"><span class="material-symbols-outlined text-base">content_copy</span> Salin Semua</button><button class="px-4 py-2 bg-surface-container text-on-surface hover:bg-surface-container-high rounded-xl font-label-md text-label-md flex items-center gap-1.5 transition-colors shadow-xs" x-on:click="showUnavailable('Kode cadangan belum tersedia untuk diunduh.')" type="button"><span class="material-symbols-outlined text-base">download</span> Unduh Teks</button></div>
                            <div class="pt-2"><label class="flex items-center gap-3 cursor-pointer group select-none"><input disabled aria-disabled="true" class="w-5 h-5 rounded-sm text-secondary focus:ring-0 cursor-pointer" id="confirmCheck" type="checkbox"><span class="font-body-md text-body-md text-on-surface group-hover:text-primary transition-colors">Saya sudah simpan kodenya dengan aman.</span></label></div>
                        </section>
                        <!-- SECURITY POLICIES & PRIMARY CTAs -->
                        <div class="source-space-y-4 pt-2">
                            <div class="flex flex-col sm:flex-row items-center gap-3"><button class="w-full sm:flex-1 py-3.5 px-6 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-title-md text-title-md font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-center" x-on:click="showUnavailable('2FA belum diaktifkan. Anda dapat melanjutkan pratinjau pemilihan peran.', $el.dataset.nextUrl)" data-next-url="{{ route('onboarding.role') }}" type="button"><span class="">Lanjutkan ke Pilih Peran</span><span class="material-symbols-outlined text-xl">arrow_forward</span></button><button class="w-full sm:w-auto py-3.5 px-5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg transition-colors flex items-center justify-center gap-1.5" x-on:click="showUnavailable('Kontak Bantuan IT belum tersedia.')" type="button"><span class="material-symbols-outlined text-lg">support_agent</span><span class="">Bantuan IT</span></button></div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant text-center pt-1">Sesi otomatis selesai jika 30 menit tidak aktif.</p></div>
                    </div>
                    <!-- Footer Help Line -->
                    <footer class="mt-8 pt-6 border-none text-center">

                    </footer>
                </main>
            </div>
    </div></main>
    </x-layouts.auth>
