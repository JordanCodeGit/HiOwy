@props(['waliKelasList' => []])
<template x-teleport="body">
<div id="forward-modal" x-cloak x-show="modal === 'forward-modal'" x-transition.opacity @click.self="closeModal()" @keydown.escape.window="closeModal()" @keydown.tab="trapFocus($event)" role="dialog" aria-modal="true" aria-labelledby="forward-heading" tabindex="-1" class="bk-modal fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/40 p-4 backdrop-blur-sm">
    <form @submit.prevent="submitForward()" class="my-auto flex w-full max-w-lg flex-col gap-5 rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between gap-3">
            <h2 id="forward-heading" class="font-headline-sm text-lg font-bold text-slate-900">Kirim Ringkasan ke Wali Kelas</h2>
            <button type="button" @click="closeModal()" aria-label="Tutup ringkasan" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <label class="flex flex-col gap-2 text-xs font-semibold text-slate-700">Wali Kelas
            <select x-model="walasClass" class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                @if(!empty($waliKelasList) && count($waliKelasList) > 0)
                    @foreach($waliKelasList as $walas)
                        <option value="{{ $walas['class'] }}">{{ $walas['label'] }}</option>
                    @endforeach
                @else
                    <option value="">Belum ada wali kelas terdaftar (Undang staf terlebih dahulu)</option>
                @endif
            </select>
        </label>
        <label class="flex flex-col gap-2 text-xs font-semibold text-slate-700">Ringkasan netral
            <textarea x-model="summary" rows="5" required maxlength="2000" class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm font-normal focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Tuliskan ringkasan yang diperlukan untuk pendampingan kelas..."></textarea>
        </label>
        <p class="text-xs text-slate-500">Tanpa detail identitas yang dapat mengidentifikasi atau membahayakan pelapor.</p>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" @click="closeModal()" class="rounded-lg bg-slate-100 hover:bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition-colors">Batal</button>
            <button type="submit" :disabled="loading" class="rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition-colors shadow-xs">
                <span x-show="!loading">Kirim Ringkasan</span>
                <span x-show="loading">Mengirim...</span>
            </button>
        </div>
    </form>
</div>
</template>
