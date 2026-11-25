<x-layouts.auth title="Aktivasi Akun Staf" headline="Selamat datang di ruang aman sekolah" pose="1">
    <div x-data="{ showPass: false, showConfirm: false }">
        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight text-center">
            Buat Kata Sandi Akun Staf
        </h1>
        <p class="mt-2 font-body-md text-body-md text-on-surface-variant text-center">
            Setelah menyimpan kata sandi, Anda akan diarahkan untuk mengaktifkan 2FA.
        </p>

        <div class="mt-5 p-4 rounded-xl bg-surface-container-low border border-outline-variant/30 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div>
                <span class="text-on-surface-variant block">Alamat Email</span>
                <span class="font-semibold text-on-surface block truncate">{{ $staff->email }}</span>
            </div>
            <div>
                <span class="text-on-surface-variant block">Peran Ditugaskan</span>
                <span class="font-semibold text-secondary block">
                    @if($staff->role === 'walikelas')
                        Wali Kelas {{ $staff->class_name ?? '' }}
                    @elseif($staff->role === 'kesiswaan')
                        Tim Kesiswaan
                    @else
                        Guru BK / Konselor
                    @endif
                </span>
            </div>
        </div>

        @if ($errors->any())
            <div class="mt-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 flex items-start gap-3 text-body-sm">
                <span class="material-symbols-outlined text-title-md shrink-0 mt-0.5">error</span>
                <div class="flex-1 font-medium">{{ $errors->first() }}</div>
            </div>
        @endif

        <form method="POST" action="{{ $acceptUrl }}" class="mt-6 source-space-y-4">
            @csrf

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md font-semibold text-on-surface" for="staff-name">Nama Lengkap &amp; Gelar</label>
                <input id="staff-name" name="name" type="text" maxlength="120" required value="{{ old('name', $staff->name) }}" class="w-full px-4 py-3 bg-surface-container-lowest text-on-surface rounded-xl border border-outline-variant/40 focus:border-secondary focus:outline-hidden text-sm">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md font-semibold text-on-surface" for="staff-password">Kata Sandi Baru</label>
                <div class="relative">
                    <input id="staff-password" name="password" :type="showPass ? 'text' : 'password'" required minlength="10" autocomplete="new-password" placeholder="Minimal 10 karakter (huruf besar, kecil, angka, simbol)" class="w-full px-4 pr-11 py-3 bg-surface-container-lowest text-on-surface rounded-xl border border-outline-variant/40 focus:border-secondary focus:outline-hidden text-sm">
                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface cursor-pointer">
                        <span class="material-symbols-outlined text-[20px]" x-text="showPass ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md font-semibold text-on-surface" for="staff-password-confirm">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <input id="staff-password-confirm" name="password_confirmation" :type="showConfirm ? 'text' : 'password'" required minlength="10" autocomplete="new-password" placeholder="Ketik ulang kata sandi baru" class="w-full px-4 pr-11 py-3 bg-surface-container-lowest text-on-surface rounded-xl border border-outline-variant/40 focus:border-secondary focus:outline-hidden text-sm">
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface cursor-pointer">
                        <span class="material-symbols-outlined text-[20px]" x-text="showConfirm ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>
            </div>

            <label class="flex items-start gap-2.5 text-xs text-on-surface-variant cursor-pointer pt-1">
                <input type="checkbox" name="terms" value="1" required class="mt-0.5 rounded border-outline-variant text-secondary focus:ring-secondary">
                <span>Saya menyetujui Ketentuan Layanan dan berkomitmen menjaga kerahasiaan data laporan siswa {{ $schoolName }}.</span>
            </label>

            <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-title-md text-title-md font-bold transition-all shadow-md flex items-center justify-center gap-2 cursor-pointer mt-2">
                <span>Simpan Sandi &amp; Lanjut Setup 2FA</span>
                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
            </button>
        </form>
    </div>
</x-layouts.auth>
