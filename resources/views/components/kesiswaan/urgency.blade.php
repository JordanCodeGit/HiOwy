@props(['value'])
@if($value)
<span @class(['inline-flex items-center gap-1.5 rounded-md border px-2.5 py-1.5 text-xs font-semibold whitespace-nowrap', 'bg-rose-50 border-rose-200 text-rose-700' => $value === 'Darurat', 'bg-amber-50 border-amber-200 text-amber-800' => $value === 'Medium', 'bg-emerald-50 border-emerald-200 text-emerald-700' => $value === 'Easy'])>
    <span class="material-symbols-outlined text-base">{{ $value === 'Darurat' ? 'error' : ($value === 'Medium' ? 'warning' : 'check_circle') }}</span>{{ $value }}
</span>
@endif
