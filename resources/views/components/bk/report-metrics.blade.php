@props(['unread', 'urgent', 'overdue'])
<section class="report-metrics staff-metrics metric-featured grid grid-cols-1 gap-4 md:grid-cols-3" aria-label="Prioritas laporan">
    @foreach ([
        ['Belum dibaca', 'mark_email_unread', $unread, 'Laporan baru', 'Menunggu telaah awal konselor', ''],
        ['Perhatian utama', 'e911_emergency', $urgent, 'Darurat belum dibaca', 'Indikasi ancaman fisik atau bahaya', 'danger'],
        ['Tanpa update > 3 hari', 'history_toggle_off', $overdue, 'Perlu tindak lanjut', 'Penanganan belum diperbarui', 'overdue'],
    ] as [$title, $icon, $count, $label, $caption, $priority])
        <button type="button" class="report-metric" data-priority="{{ $priority }}" :aria-pressed="priorityFilter === '{{ $priority }}'">
            <span class="metric-title">{{ $title }} <span class="material-symbols-outlined metric-icon" aria-hidden="true">{{ $icon }}</span></span>
            <span class="metric-value">{{ $count }} <small>{{ $label }}</small></span>
            <span class="metric-caption">{{ $caption }}</span>
        </button>
    @endforeach
</section>
