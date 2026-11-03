<template x-teleport="body">
<div id="forward-modal" x-cloak x-show="modal === 'forward-modal'" x-transition.opacity @click.self="closeModal()" @keydown.escape.window="closeModal()" @keydown.tab="trapFocus($event)" role="dialog" aria-modal="true" aria-labelledby="forward-heading" tabindex="-1" class="bk-modal fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/40 p-4 backdrop-blur-sm">
    <form method="POST" action="{{ route('frontend.unavailable', ['page' => 'bk']) }}" @submit.prevent="submitUnavailable($event)" class="my-auto flex w-full max-w-lg flex-col gap-5 rounded-2xl bg-white p-6 shadow-xl">
        @csrf
        <div class="flex items-center justify-between gap-3"><h2 id="forward-heading" class="font-headline-sm text-lg font-bold">Kirim Ringkasan ke Wali Kelas</h2><button type="button" @click="closeModal()" aria-label="Tutup ringkasan"><span class="material-symbols-outlined">close</span></button></div>
        <label class="flex flex-col gap-2 text-xs font-semibold">Wali Kelas<select class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm" required><option value="wali-8a">Hendra S., M.Pd. (Wali 8A)</option></select></label>
        <label class="flex flex-col gap-2 text-xs font-semibold">Ringkasan netral<textarea x-model="summary" rows="5" required maxlength="2000" class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm font-normal" placeholder="Tuliskan ringkasan yang diperlukan untuk pendampingan kelas..."></textarea></label>
        <p class="text-xs text-slate-500">Tanpa detail yang dapat mengidentifikasi pelapor.</p>
        <div class="flex justify-end gap-3"><button type="button" @click="closeModal()" class="rounded-lg bg-slate-100 px-4 py-2 text-sm">Batal</button><button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Kirim Ringkasan</button></div>
    </form>
</div>
</template>
