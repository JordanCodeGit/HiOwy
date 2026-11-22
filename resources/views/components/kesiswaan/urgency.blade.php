@props(['value'])
@if($value)
@php
    $norm = strtolower((string) $value);
    $display = in_array($norm, ['darurat', 'urgent'], true)
        ? 'Darurat'
        : (in_array($norm, ['sedang', 'medium', 'tinggi', 'penting'], true) ? 'Sedang' : 'Rendah');
@endphp
<span @class(['inline-flex items-center gap-1.5 rounded-md border px-2.5 py-1.5 text-xs font-semibold whitespace-nowrap', 'bg-rose-50 border-rose-200 text-rose-700' => $display === 'Darurat', 'bg-amber-50 border-amber-200 text-amber-800' => $display === 'Sedang', 'bg-emerald-50 border-emerald-200 text-emerald-700' => $display === 'Rendah'])>
    <span class="material-symbols-outlined text-base">{{ $display === 'Darurat' ? 'error' : ($display === 'Sedang' ? 'warning' : 'check_circle') }}</span>{{ $display }}
</span>
@endif
