@props(['defaultOrder' => 'Prioritas'])
<label class="report-order">
    <span>Urutkan</span>
    <select aria-label="Urutkan laporan" @change="sortReports($event.target.value)">
        <option value="default">{{ $defaultOrder }}</option>
        @if ($defaultOrder !== 'Terbaru')<option value="newest">Terbaru</option>@endif
        <option value="oldest">Terlama</option>
    </select>
</label>
