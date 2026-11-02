<div x-cloak x-show="dateFilterOpen || Object.values(filters).some(filter => filter.value === 'custom')" x-transition.opacity class="my-3 flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4 text-xs">
    <label class="flex flex-col gap-1.5 font-medium">Dari tanggal<input type="date" x-model="dateFrom" :max="dateTo || null" @change="selected = []" class="rounded-lg border border-slate-200 p-2"></label>
    <label class="flex flex-col gap-1.5 font-medium">Sampai tanggal<input type="date" x-model="dateTo" :min="dateFrom || null" @change="selected = []" class="rounded-lg border border-slate-200 p-2"></label>
    <button type="button" @click="dateFrom = ''; dateTo = ''; selected = []" class="rounded-lg bg-slate-100 px-3 py-2">Reset tanggal</button>
    <span class="text-slate-500">Tanggal pada data contoh: September–Oktober 2024.</span>
</div>
