<x-layouts.auth title="Aktivasi Authenticator 2FA" body-class="bg-surface font-body-md text-body-md text-on-surface min-h-screen" :medium-fonts="false">
    <main class="w-full min-h-screen bg-surface flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-lg bg-surface-container-lowest rounded-3xl shadow-xl border border-outline-variant/30 overflow-hidden text-on-surface">
            <!-- Header Banner -->
            <div class="bg-primary-container text-on-primary p-6 sm:p-8 text-center relative overflow-hidden">
                <div class="absolute -top-24 -left-24 w-64 h-64 rounded-full bg-secondary opacity-20 blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-64 h-64 rounded-full bg-tertiary-fixed opacity-15 blur-2xl pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-12 h-12 rounded-2xl bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center mb-3 shadow-md">
                        <span class="material-symbols-outlined text-headline-sm">phonelink_lock</span>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-primary-fixed font-label-sm text-label-sm mb-2 backdrop-blur-md">
                        <span class="material-symbols-outlined text-sm text-tertiary-fixed">verified</span>
                        Tautan Resmi dari Gmail
                    </span>
                    <h1 class="font-headline-md text-headline-md font-bold tracking-tight text-on-primary">
                        Hubungkan ke Authenticator
                    </h1>
                    <p class="font-body-sm text-body-sm text-primary-fixed-dim mt-1">
                        {{ $user->email }}
                    </p>
                </div>
            </div>

            <!-- Body Area -->
            <div class="p-6 sm:p-8 space-y-6">
                <!-- Action 1: Mobile direct deep link -->
                <div class="text-center space-y-3">
                    <p class="font-body-md text-body-md text-on-surface font-semibold">
                        Jika Anda membuka halaman ini di ponsel:
                    </p>
                    <a href="{{ $otpauthUri }}" id="open-auth-btn" class="w-full py-4 px-6 rounded-2xl bg-primary text-on-primary font-headline-sm text-headline-sm hover:bg-surface-tint active:scale-[0.99] transition-all flex items-center justify-center gap-3 shadow-lg shadow-primary/20 cursor-pointer">
                        <span class="material-symbols-outlined text-title-lg">open_in_new</span>
                        <span>Buka di Google Authenticator</span>
                    </a>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">
                        Ponsel Anda akan otomatis membuka aplikasi authenticator dan menambahkan akun HiOwy.
                    </p>
                </div>

                <!-- Divider -->
                <div class="relative flex items-center justify-center">
                    <div class="w-full bg-outline-variant h-px opacity-30"></div>
                    <span class="absolute px-3 bg-surface-container-lowest font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                        Atau Scan QR Code
                    </span>
                </div>

                <!-- Action 2: QR Code & Manual Key for Desktop/Laptop -->
                <div class="p-5 rounded-2xl bg-surface-container-low border border-outline-variant/30 flex flex-col sm:flex-row items-center gap-5">
                    <div class="bg-white p-3 rounded-xl shadow-xs shrink-0 text-center">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code Authenticator" class="w-32 h-32 object-contain mx-auto" />
                    </div>
                    <div class="space-y-2 text-center sm:text-left flex-1 min-w-0">
                        <p class="font-body-sm text-body-sm font-semibold text-on-surface">
                            Buka di Laptop?
                        </p>
                        <p class="font-body-xs text-body-sm text-on-surface-variant leading-relaxed">
                            Scan QR di samping dengan kamera aplikasi Google Authenticator di ponsel Anda.
                        </p>
                        <div class="pt-1">
                            <span class="font-label-sm text-label-sm text-on-surface-variant block mb-1">Kunci Manual:</span>
                            <code class="px-2.5 py-1 bg-surface-container rounded-lg font-mono text-xs font-bold text-secondary block truncate select-all">
                                {{ $user->two_factor_secret }}
                            </code>
                        </div>
                    </div>
                </div>

                <!-- Next Step Notice -->
                <div class="p-4 rounded-2xl bg-secondary/10 border border-secondary/20 flex items-start gap-3 text-body-sm text-on-surface">
                    <span class="material-symbols-outlined text-secondary text-title-md shrink-0 mt-0.5">info</span>
                    <div class="space-y-1">
                        <span class="font-semibold block text-secondary">Langkah Terakhir:</span>
                        <p class="text-on-surface-variant">
                            Setelah akun tersimpan di aplikasi authenticator, lihat 6 digit angka yang muncul, lalu kembali ke halaman verifikasi HiOwy untuk memasukkannya.
                        </p>
                    </div>
                </div>

                <!-- Action Link Back to Verify Page -->
                <div class="pt-2">
                    <a href="{{ route('two-factor.challenge') }}" class="w-full py-3 px-5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-title-md text-title-md transition-all flex items-center justify-center gap-2 cursor-pointer border border-outline-variant/30">
                        <span>Lanjut Masukkan 6 Digit Kode</span>
                        <span class="material-symbols-outlined text-title-md">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Auto-launch script on mobile browsers -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Check if mobile device
            var isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
            if (isMobile) {
                // Trigger deep link automatically after 400ms
                setTimeout(function () {
                    window.location.href = "{{ $otpauthUri }}";
                }, 400);
            }
        });
    </script>
</x-layouts.auth>
