@props(['title' => 'Gagal Verifikasi'])
@if ($errors->any())
    <div x-data="{ open: true }" x-show="open" role="alert"
        class="fixed top-6 right-6 z-50 flex items-center justify-between gap-3 px-4 py-3 rounded-xl bg-surface-container-lowest text-on-surface shadow-lg border border-error/20 transition-all duration-200 max-w-sm sm:max-w-md">
        <div class="flex items-center gap-2.5 flex-1 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-error-container text-on-error-container flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-title-md" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">error</span>
            </div>
            <div class="font-body-sm text-body-sm leading-snug">
                <span class="font-semibold text-error block">{{ $title }}</span>
                {{ $errors->first() }}
            </div>
        </div>
        <button type="button" x-on:click="open = false" aria-label="Tutup pemberitahuan"
            class="p-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface shrink-0 transition-colors flex items-center justify-center">
            <span class="material-symbols-outlined text-body-lg" aria-hidden="true">close</span>
        </button>
    </div>
@endif
