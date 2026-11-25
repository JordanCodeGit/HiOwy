@props(['icon', 'label', 'value'])
<div class="staff-stat-card">
    <span class="material-symbols-outlined stat-icon" aria-hidden="true">{{ $icon }}</span>
    <div>
        <p class="text-xs font-semibold text-slate-500">{{ $label }}</p>
        <p class="mt-1 text-2xl font-extrabold tracking-tight">{{ $value }}</p>
        @if ($slot->isNotEmpty())<p class="mt-1 text-xs text-slate-500">{{ $slot }}</p>@endif
    </div>
</div>
