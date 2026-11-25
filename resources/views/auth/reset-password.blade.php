<x-layouts.auth title="Reset Kata Sandi" headline="Lindungi kembali akun Anda" pose="6">
    <div x-data="{ showPass: false, showConfirm: false }">
        <!-- Top Navigation & Return Link -->
        <div class="flex w-full shrink-0 items-start justify-between gap-4">
            <a class="inline-flex items-center gap-2 py-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high/60 transition-all font-label-md text-label-md group" href="{{ route('login') }}">
                <span class="material-symbols-outlined text-base transition-transform group-hover:-translate-x-1">arrow_back</span>
                <span>Kembali ke Halaman Masuk</span>
            </a>
        </div>

        <!-- Centered Interactive Reset Form -->
        <div class="w-full max-w-xl mx-auto my-auto pt-5">
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
                        <input class="w-full pl-12 pr-12 py-3.5 bg-surface-container-lowest text-on-surface placeholder:text-outline-variant font-body-md text-body-md rounded-xl shadow-xs border border-outline-variant/30 focus:outline-hidden focus:border-secondary focus:bg-surface-container-lowest transition-all" id="new-password" placeholder="Minimal 10 karakter aman" minlength="10" required="" type="password" name="password" autocomplete="new-password" x-bind:type="showPass ? 'text' : 'password'">
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
                        <input class="w-full pl-12 pr-12 py-3.5 bg-surface-container-lowest text-on-surface placeholder:text-outline-variant font-body-md text-body-md rounded-xl shadow-xs border border-outline-variant/30 focus:outline-hidden focus:border-secondary focus:bg-surface-container-lowest transition-all" id="confirm-password" placeholder="Ketik ulang kata sandi baru" minlength="10" required="" type="password" name="password_confirmation" autocomplete="new-password" x-bind:type="showConfirm ? 'text' : 'password'">
                        <button class="absolute right-3.5 p-1 text-outline hover:text-on-surface transition-colors cursor-pointer" type="button" x-on:click="showConfirm = !showConfirm" title="Tampilkan / Sembunyikan konfirmasi sandi">
                            <span class="material-symbols-outlined text-xl" x-text="showConfirm ? 'visibility_off' : 'visibility'">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Requirements Checklist Box -->
                <div class="p-3.5 rounded-xl bg-surface-container-low text-on-surface-variant font-body-xs text-xs source-space-y-1">
                    <p class="font-semibold text-on-surface">Ketentuan Kata Sandi:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-on-surface-variant">
                        <li>Minimal 10 karakter</li>
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
</x-layouts.auth>
