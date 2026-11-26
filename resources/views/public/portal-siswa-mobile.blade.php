<div class="student-mobile-shell">
    <header class="m-header">
        <div class="m-hdr-left">
            <button type="button" class="m-back-btn" x-show="activeTab === 'track' || (!submitted && currentStep > 1)"
                @click="activeTab === 'track' ? switchTab('form') : prevStep()" aria-label="Kembali">
                <span class="material-symbols-outlined">arrow_back</span>
            </button>
            <strong class="m-brand">HiOwy!</strong>
        </div>
        <button class="m-exit-btn" type="button" @click="quickExit()"><span class="material-symbols-outlined">logout</span> Keluar cepat</button>
    </header>
    <nav class="m-subbar" aria-label="Portal siswa">
        <div class="m-tabs">
            <button type="button" class="m-tab" :aria-pressed="activeTab === 'form'" @click="switchTab('form')">Buat Aduan</button>
            <button type="button" class="m-tab" :aria-pressed="activeTab === 'track'" @click="switchTab('track')">Lacak Aduan</button>
        </div>
        <div class="m-util-icons">
            <a class="m-util-btn" aria-label="Halaman Pelajari" href="{{ route('public.learn.default') }}"><span class="material-symbols-outlined">menu_book</span></a>
            <button class="m-util-btn" type="button" aria-label="Privasi dan jam baca BK" @click="privacyModalOpen = true"><span class="material-symbols-outlined">shield_lock</span></button>
        </div>
    </nav>
    <div class="m-progress" x-show="activeTab === 'form' && !submitted">
        <div class="m-prog-labels"><span x-text="'Pertanyaan ' + currentStep + ' dari ' + totalSteps"></span><span x-text="Math.round(currentStep / totalSteps * 100) + '%'"></span></div>
        <div class="m-dashes">
            <template x-for="step in totalSteps" :key="step">
                <button type="button" :disabled="step > maxReachedStep" :aria-label="'Pertanyaan ' + step"
                    :class="{ filled: step <= currentStep }" @click="jumpToStep(step)"></button>
            </template>
        </div>
    </div>
</div>
