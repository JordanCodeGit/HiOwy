<template x-teleport="body">
<div id="text-preview" x-cloak x-show="modal === 'text-preview'" x-transition.opacity @click.self="closeModal()" @keydown.escape.window="closeModal()" @keydown.tab="trapFocus($event)" role="dialog" aria-modal="true" aria-labelledby="preview-heading" tabindex="-1" class="bk-modal fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-sm">
    <div class="my-auto flex w-full max-w-xl flex-col gap-5 rounded-2xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-100" @click.outside="closeModal()">
        <div class="flex items-start justify-between gap-4 pb-3 border-b border-slate-100">
            <div class="flex flex-col gap-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider" x-text="previewAudience || 'Portal Siswa'"></span>
                    <span class="text-xs text-slate-400 font-mono" x-text="previewCode"></span>
                </div>
                <h2 id="preview-heading" class="font-headline-sm text-headline-sm font-bold text-slate-900 mt-1.5 leading-snug" x-text="previewTitle"></h2>
            </div>
            <button type="button" @click="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors cursor-pointer shrink-0" aria-label="Tutup pratinjau">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <div class="flex items-center gap-3 text-xs text-slate-500 py-2 px-3 rounded-xl bg-slate-50 border border-slate-100">
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base text-blue-600">person</span>
                <span class="font-semibold text-slate-700" x-text="previewAuthor || 'Konselor BK'"></span>
            </span>
            <span>•</span>
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base text-slate-400">calendar_today</span>
                <span x-text="previewDate"></span>
            </span>
        </div>

        <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-100 max-h-[360px] overflow-y-auto">
            <p class="whitespace-pre-wrap text-sm leading-relaxed text-slate-800" x-text="previewContent"></p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs text-slate-400">
            <span>Tampilan pratinjau sesuai format yang dibaca siswa di portal.</span>
            <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>
</template>
