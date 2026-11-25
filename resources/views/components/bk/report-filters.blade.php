<div class="report-filters" x-data="reportFilterDialog">
    <button type="button" class="filter-trigger" aria-haspopup="dialog" @click="openFilters">
        <span class="material-symbols-outlined" aria-hidden="true">filter_list</span>
        Filter <span x-show="filterCount" x-text="'(' + filterCount + ')'"></span>
    </button>
    <dialog x-ref="dialog" class="report-filter-dialog" aria-labelledby="report-filter-title" @click.self="$el.close()">
        <form @submit.prevent="applyFilters">
            <header>
                <h2 id="report-filter-title">Filter Lanjutan Antrean</h2>
                <button type="button" aria-label="Tutup filter" @click="$refs.dialog.close()"><span class="material-symbols-outlined">close</span></button>
            </header>
            <div class="filter-fields">
                {{ $slot }}
                <div class="filter-dates" x-show="draft.period === 'custom'">
                    <label>Dari tanggal<input type="date" :disabled="draft.period !== 'custom'" x-model="draft.from" :max="draft.to || null"></label>
                    <label>Sampai tanggal<input type="date" :disabled="draft.period !== 'custom'" x-model="draft.to" :min="draft.from || null"></label>
                </div>
            </div>
            <footer>
                <button type="button" @click="clearDraft">Reset Filter</button>
                <button type="submit" class="staff-primary">Terapkan Filter</button>
            </footer>
        </form>
    </dialog>
    <template x-for="chip in appliedChips" :key="chip.key">
        <button type="button" class="filter-chip" @click="removeFilter(chip.key)" :aria-label="'Hapus filter ' + chip.label">
            <span x-text="chip.label"></span><span aria-hidden="true"> ×</span>
        </button>
    </template>
</div>
