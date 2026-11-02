@props(['id', 'title', 'action'])
<template x-teleport="body">
    <div x-cloak x-show="modal === '{{ $id }}'" x-transition.opacity class="bk-modal fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" @click.self="closeModal()" @keydown.escape.window="closeModal()">
        <section id="{{ $id }}" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" tabindex="-1" @keydown.tab="trapFocus($event)" class="w-full max-w-lg max-h-[calc(100dvh-2rem)] overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
            <h2 id="{{ $id }}-title" class="text-lg font-bold text-slate-900">{{ $title }}</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $slot }}</p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="closeModal()" class="rounded-xl border border-slate-200 px-4 py-2.5 hover:bg-slate-50">Batal</button>
                <button type="button" @click="closeModal(); unavailable($el.dataset.action)" data-action="{{ $action }}" class="rounded-xl bg-primary text-on-primary px-4 py-2.5 hover:bg-primary-container">Konfirmasi</button>
            </div>
        </section>
    </div>
</template>
