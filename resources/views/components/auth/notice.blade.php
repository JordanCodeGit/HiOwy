<div
    x-data="authNotice"
    x-on:auth-notice.window="open($event.detail)"
    x-on:keydown.escape.window="visible = false"
    data-message="{{ session('frontend_notice', '') }}"
    data-next-url="{{ session('frontend_next_url', '') }}"
>
    <div x-cloak x-show="visible" role="status" aria-live="polite"
        class="fixed top-6 right-6 z-50 flex items-start gap-3 px-4 py-3 rounded-xl bg-surface-container-lowest text-on-surface shadow-lg border border-secondary/20 w-[calc(100vw-3rem)] max-w-sm sm:max-w-md">
        <span class="material-symbols-outlined text-secondary text-title-lg" aria-hidden="true">info</span>
        <div class="font-body-sm text-body-sm leading-snug">
            <p class="font-semibold" x-text="title"></p>
            <p x-text="message"></p>
            <a x-cloak x-show="nextUrl" x-bind:href="nextUrl || null"
                class="inline-block mt-2 font-semibold text-secondary hover:underline">Lanjutkan pratinjau</a>
        </div>
        <button type="button" x-on:click="visible = false" aria-label="Tutup pemberitahuan"
            class="p-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant shrink-0">
            <span class="material-symbols-outlined text-body-lg" aria-hidden="true">close</span>
        </button>
    </div>
    <noscript>
        <p class="fixed bottom-4 left-4 right-4 rounded-xl bg-surface-container-low p-4 font-body-sm text-body-sm text-on-surface">
            {{ session('frontend_notice', 'Aktifkan JavaScript untuk menggunakan interaksi pratinjau. Autentikasi belum terintegrasi.') }}
            @if (session('frontend_next_url'))
                <a href="{{ session('frontend_next_url') }}" class="text-secondary underline">Lanjutkan pratinjau</a>
            @endif
        </p>
    </noscript>
</div>
