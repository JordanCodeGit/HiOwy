<template x-teleport="body">
<div id="text-preview" x-cloak x-show="modal === 'text-preview'" x-transition.opacity @click.self="closeModal()" @keydown.escape.window="closeModal()" @keydown.tab="trapFocus($event)" role="dialog" aria-modal="true" aria-labelledby="preview-heading" tabindex="-1" class="bk-modal fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/40 p-4 backdrop-blur-sm">
    <div class="my-auto flex w-full max-w-xl flex-col gap-5 rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-start justify-between gap-4"><h2 id="preview-heading" class="font-headline-sm text-headline-sm" x-text="previewTitle"></h2><button type="button" @click="closeModal()" aria-label="Tutup pratinjau"><span class="material-symbols-outlined">close</span></button></div>
        <p class="whitespace-pre-wrap text-sm leading-relaxed text-on-surface-variant" x-text="previewContent"></p>
        <p class="text-xs text-outline">Pratinjau materi dari desain. Materi belum diterbitkan ke portal siswa.</p>
    </div>
</div>
</template>
