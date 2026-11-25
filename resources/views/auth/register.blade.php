<x-layouts.auth title="Daftar Staf" headline="Selamat datang di ruang aman sekolah" description="Aktifkan akun staf melalui undangan resmi sekolah.">
    <h1>Daftar akun staf</h1>
    <p class="mt-2 text-sm text-slate-600">Tempel tautan undangan dari Koordinator BK atau Admin Sekolah untuk melanjutkan pendaftaran.</p>
    <form class="mt-6 grid gap-3" x-data="staffInvitation" @submit.prevent="openInvitation" data-invitation-path="{{ route('staff.invitation.show', ['staff' => 'STAFF_ID'], false) }}">
        <label class="font-label-md text-label-md" for="invitation-url">Tautan undangan</label>
        <input id="invitation-url" type="url" x-ref="invitation" required placeholder="Tempel tautan dari email sekolah" @input="$refs.invitation.setCustomValidity('')" class="w-full rounded-xl px-4 py-3">
        <button class="bg-primary text-white rounded-xl px-4 py-3 font-bold" type="submit">Lanjutkan pendaftaran</button>
    </form>
    <a class="mt-5 inline-block text-sm font-semibold text-secondary" href="{{ route('login') }}">Kembali ke halaman masuk</a>
</x-layouts.auth>
