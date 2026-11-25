@if (request('halaman') === 'daftar')
    @include('auth.register')
@else
<x-layouts.auth title="Masuk" headline="Ruang Aman & Terlindung" pose="1" description="Sistem pelaporan dan pendampingan siswa terpadu.">
    <div x-data="{ itModal: false }" @keydown.escape.window="itModal = false">
        <!-- Header Title Block -->
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Masuk</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1.5">Silakan masuk ke akun konselor atau staf sekolah Anda.</p>
        </div>

        <!-- Status / Feedback Alerts -->
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 flex items-start gap-3 text-body-sm shadow-xs">
                <span class="material-symbols-outlined text-title-md shrink-0 mt-0.5">error</span>
                <div class="flex-1 font-medium">
                    {{ $errors->first() }}
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center gap-3 text-body-sm shadow-xs">
                <span class="material-symbols-outlined text-title-md shrink-0">check_circle</span>
                <div class="flex-1 font-medium">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <!-- Form Elements -->
        <form class="auth-login-form flex flex-col gap-4" method="POST" action="{{ route('login.attempt') }}" x-data="{
            email: @js(old('email', $rememberedEmail ?? '')),
            remember: {{ old('remember', ($isRemembered ?? true) ? 'true' : 'false') }},
            init() {
                if (!this.email) {
                    const savedEmail = localStorage.getItem('hiowy_remember_email');
                    if (savedEmail) {
                        this.email = savedEmail;
                        this.remember = true;
                    }
                }
            },
            onSubmit() {
                if (this.remember && this.email) {
                    localStorage.setItem('hiowy_remember_email', this.email);
                } else if (!this.remember) {
                    localStorage.removeItem('hiowy_remember_email');
                }
            }
        }" @submit="onSubmit">
            @csrf
            <!-- Email Input -->
            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-on-surface font-semibold flex items-center justify-between" for="counselor-email">
                    <span>Email Admin</span>
                </label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute right-3.5 text-outline text-title-md pointer-events-none">alternate_email</span>
                    <input class="w-full pl-11 pr-4 py-3 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-xl outline-hidden shadow-xs focus:shadow-md transition-all placeholder:text-outline-variant border border-outline-variant/30 focus:border-secondary" id="counselor-email" placeholder="nama@sekolah.sch.id" required="" type="email" name="email" x-model="email" autocomplete="username">
                </div>
            </div>

            <!-- Password Input -->
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center justify-between">
                    <label class="font-label-md text-label-md text-on-surface font-semibold" for="counselor-password">Kata Sandi</label>

                </div>
                <div class="relative flex items-center" x-data="{ visible: false }">
                    <input class="w-full pl-11 pr-11 py-3 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-xl outline-hidden shadow-xs focus:shadow-md transition-all placeholder:text-outline-variant border border-outline-variant/30 focus:border-secondary" id="counselor-password" placeholder="••••••••••••" required="" type="password" name="password" autocomplete="current-password" x-bind:type="visible ? 'text' : 'password'">
                    <button class="absolute right-3 p-1.5 text-outline hover:text-on-surface transition-colors rounded-lg cursor-pointer" id="toggle-pwd-btn" title="Tampilkan / Sembunyikan sandi" type="button" x-on:click="visible = !visible" x-bind:aria-label="visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" x-bind:aria-pressed="visible">
                        <span class="material-symbols-outlined text-title-md" id="eye-icon" x-text="visible ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>

            </div>

            <!-- Remember me -->
            <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer select-none" for="remember-me">
                    <input class="w-4 h-4 rounded-sm text-secondary focus:ring-0 cursor-pointer" type="checkbox" name="remember" id="remember-me" value="1" x-model="remember">
                    <span class="font-body-sm text-body-sm text-on-surface">Ingat saya</span>
                </label>
                <a class="font-label-sm text-label-sm text-secondary hover:underline font-semibold" href="{{ route('password.request') }}">Lupa Kata Sandi?</a>
            </div>

            <!-- Primary Submit Button -->
            <button class="w-full mt-2 py-3.5 px-6 rounded-xl bg-primary text-on-primary font-headline-sm text-headline-sm hover:bg-surface-tint active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg cursor-pointer" id="btn-submit" type="submit">
                <span>Lanjutkan ke Verifikasi 2FA</span>
                <span class="material-symbols-outlined text-title-md">arrow_forward</span>
            </button>
        </form>

        <p class="mt-5 text-center font-body-xs text-body-sm text-on-surface-variant">
            Sudah menerima undangan staf? <a class="auth-register-link" href="{{ route('login', ['halaman' => 'daftar']) }}">Daftar sekarang</a>
        </p>

        <!-- Help Support Button -->
        <div class="mt-2 text-center">
            <span class="font-body-sm text-body-sm text-on-surface-variant">Kendala akses akun? </span>
            <button class="font-label-sm text-label-sm text-secondary hover:underline font-semibold cursor-pointer" type="button" x-on:click="itModal = true">Hubungi Bantuan IT BK</button>
        </div>
        <!-- MODAL 1: Bantuan IT BK -->
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
                    <p class="text-on-surface-variant">Jika Anda mengalami kendala sandi terkunci, perangkat authenticator 2FA hilang, atau mutasi staf sekolah, hubungi unit IT:</p>
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
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-secondary text-title-sm">location_on</span>
                            <span class="font-semibold">Ruang IT:</span>
                            <span class="text-on-surface">Gedung Utama Lantai 2</span>
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
@endif
