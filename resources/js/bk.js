import { manageRows } from './bk-data';
import { lockOverlayScroll, unlockOverlayScroll } from './overlay-scroll';

function modalState() {
    return {
        modal: '',
        modalTrigger: null,
        openModal(id) {
            if (this.modal) this.closeModal();
            this.modalTrigger = document.activeElement;
            this.modal = id;
            lockOverlayScroll(document.getElementById(id));
            this.$nextTick(() => {
                const dialog = document.getElementById(id);
                (dialog?.querySelector('input:not([type="hidden"]), textarea, button') || dialog)?.focus({ preventScroll: true });
            });
        },
        closeModal() {
            if (!this.modal) return;
            unlockOverlayScroll(document.getElementById(this.modal));
            this.modal = '';
            this.modalTrigger?.focus({ preventScroll: true });
        },
        trapFocus(event) {
            const elements = [...event.currentTarget.querySelectorAll('button, a[href], input, select, textarea, [tabindex="0"]')]
                .filter(element => !element.disabled && element.offsetParent !== null);
            const first = elements[0];
            const last = elements.at(-1);
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last?.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first?.focus();
            }
        },
    };
}

const normalize = value => value.toLocaleLowerCase('id').replace(/\s+/g, ' ').trim();

export function registerBk(Alpine, navigateTo) {
    Alpine.data('bkShell', () => ({
        sidebarOpen: false,
        mobileViewport: false,
        notificationsOpen: false,
        profileOpen: false,
        notificationsRead: false,
        notificationFilter: 'all',
        quickSearch: '',
        mediaQuery: null,
        viewportListener: null,

        init() {
            this.mediaQuery = window.matchMedia('(max-width: 1023px)');
            this.mobileViewport = this.mediaQuery.matches;
            this.viewportListener = event => {
                this.mobileViewport = event.matches;
                if (!event.matches) this.sidebarOpen = false;
            };
            this.mediaQuery.addEventListener('change', this.viewportListener);
            try {
                this.notificationsRead = sessionStorage.getItem('hiowy-preview-notifications-read') === 'yes';
            } catch { /* Preview controls also work without browser storage. */ }
        },
        destroy() {
            this.mediaQuery?.removeEventListener('change', this.viewportListener);
        },
        closeOverlays() {
            this.sidebarOpen = false;
            this.notificationsOpen = false;
            this.profileOpen = false;
        },
        unavailable(action = 'Tindakan ini') {
            this.$dispatch('auth-notice', {
                title: 'Belum terintegrasi',
                message: `${action} memerlukan backend. Data contoh tidak berubah dan tidak ada data yang dikirim atau disimpan.`,
            });
        },
        previewOtherReport(url) {
            this.$dispatch('auth-notice', {
                title: 'Detail contoh terbatas',
                message: 'Desain detail yang tersedia menggunakan laporan #SR-2024-8042. Anda dapat melanjutkan untuk melihat contoh tampilannya.',
                nextUrl: url,
            });
        },
        submitUnavailable(event) {
            if (event.currentTarget.reportValidity()) this.unavailable('Penyimpanan formulir');
        },
        markNotificationsRead() {
            this.notificationsRead = true;
            try {
                sessionStorage.setItem('hiowy-preview-notifications-read', 'yes');
            } catch { /* State remains available for the current page. */ }
        },
        searchReports(url) {
            if (document.querySelector('[data-report-list], [x-data="bkTable(\'dashboard\')"]')) {
                this.$dispatch('bk-search', { query: this.quickSearch });
            } else {
                this.$dispatch('auth-notice', {
                    title: 'Pencarian data',
                    message: 'Gunakan pencarian di halaman utama portal untuk mencari data contoh.',
                    nextUrl: url,
                });
            }
        },
        async copyText(value) {
            try {
                await navigator.clipboard.writeText(value);
                this.$dispatch('auth-notice', { title: 'Tersalin', message: 'Teks contoh telah disalin ke clipboard.' });
            } catch {
                this.$dispatch('auth-notice', { title: 'Belum tersalin', message: 'Browser tidak mengizinkan clipboard. Pilih teks dan salin secara manual.' });
            }
        },
    }));

    Alpine.data('bkManage', () => ({
        ...modalState(),
        tab: 'jenis',
        categories: structuredClone(manageRows.categories),
        locations: structuredClone(manageRows.locations),
        dragging: null,
        dropTarget: '',
        orderMessage: '',
        editing: false,
        categoryLabel: '',
        categoryActive: true,
        categoryPermanent: false,
        locationLabel: '',
        staffName: '',
        staffEmail: '',
        staffRole: 'kesiswaan',
        staffClass: '7A',
        staffStatus: 'pending',
        qrQuery: '',

        init() {
            const tab = window.location.hash.slice(1);
            if (['jenis', 'lokasi', 'qr', 'staf'].includes(tab)) this.tab = tab;
        },
        startDrag(event, list, id) {
            if (window.matchMedia('(max-width: 1023px)').matches || this[list].find(item => item.id === id)?.permanent) {
                event.preventDefault();
                return;
            }
            this.dragging = { list, id };
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', id);
        },
        dragOver(list, id) {
            if (this.dragging?.list === list && !this[list].find(item => item.id === id)?.permanent) this.dropTarget = id;
        },
        dropRow(list, targetId) {
            if (this.dragging?.list !== list) return;
            const from = this[list].findIndex(item => item.id === this.dragging.id);
            const to = this[list].findIndex(item => item.id === targetId);
            this.reorder(list, from, to);
            this.endDrag();
        },
        moveRow(list, id, direction) {
            const index = this[list].findIndex(item => item.id === id);
            this.reorder(list, index, index + direction);
        },
        reorder(list, from, to) {
            if (from < 0 || to < 0 || to >= this[list].length || from === to || this[list][from].permanent || this[list][to].permanent) return;
            const rows = [...this[list]];
            const [item] = rows.splice(from, 1);
            rows.splice(to, 0, item);
            this[list] = rows;
            this.orderMessage = `${item.label} dipindahkan ke urutan ${to + 1}. Urutan ini hanya untuk pratinjau dan kembali saat halaman dimuat ulang.`;
        },
        endDrag() {
            this.dragging = null;
            this.dropTarget = '';
        },
        openCategory(item = null) {
            this.editing = !!item;
            this.categoryLabel = item?.label || '';
            this.categoryActive = item?.active ?? true;
            this.categoryPermanent = item?.permanent ?? false;
            this.openModal('modal-kategori');
        },
        openLocation(item = null) {
            this.editing = !!item;
            this.locationLabel = item?.label || '';
            this.openModal('modal-tambah-lokasi');
        },
        openInvite() {
            this.staffName = '';
            this.staffEmail = '';
            this.staffRole = 'kesiswaan';
            this.staffClass = '7A';
            this.staffStatus = 'pending';
            this.openModal('modal-undang-staf');
        },
        editStaff(row) {
            this.staffName = row.querySelector('.col-nama').textContent.trim();
            this.staffEmail = row.querySelector('.col-email').textContent.trim();
            const role = row.querySelector('.role-text').textContent;
            this.staffRole = role.includes('Wali') ? 'walikelas' : 'kesiswaan';
            this.staffClass = role.match(/\d+[A-Z]/)?.[0] || '7A';
            this.staffStatus = row.querySelector('.status-text').textContent.trim() === 'Aktif' ? 'aktif' : 'pending';
            this.openModal('modal-edit-staf');
        },
        deleteStaff(row) {
            this.staffName = row.querySelector('.col-nama').textContent.trim();
            this.openModal('modal-hapus-staf');
        },
        validModal() {
            return [...document.getElementById(this.modal).querySelectorAll('input:not([type="hidden"]), select')]
                .filter(input => input.offsetParent !== null).every(input => input.reportValidity());
        },
        saveModal() {
            if (this.validModal()) this.unavailable('Penyimpanan perubahan');
        },
        submitInvite() {
            if (this.validModal()) this.unavailable('Pengiriman undangan staf');
        },
    }));

    Alpine.data('bkTable', kind => ({
        ...modalState(),
        kind,
        query: '',
        statusTab: 0,
        priorityFilter: '',
        filters: {},
        selected: [],
        rows: [],
        pageSize: 25,
        dateFrom: '',
        dateTo: '',

        init() {
            this.rows = [...this.$root.querySelectorAll('[data-preview-row]')].map((row, index) => ({
                index,
                text: normalize(row.textContent),
                cells: [...row.cells].map(cell => normalize(cell.textContent)),
            }));
        },
        get visibleCount() {
            return this.rows.filter(row => this.rowVisible(row.index)).length;
        },
        get allSelected() {
            const visible = this.rows.filter(row => this.rowVisible(row.index));
            return visible.length > 0 && visible.every(row => this.selected.includes(String(row.index)));
        },
        selectVisible(checked) {
            this.selected = checked ? this.rows.filter(row => this.rowVisible(row.index)).map(row => String(row.index)) : [];
        },
        filterChanged(event) {
            const select = event.target;
            if (select.options[0]?.textContent.includes('/ halaman')) {
                this.pageSize = Number.parseInt(select.selectedOptions[0].textContent, 10) || 25;
            } else {
                const index = [...this.$root.querySelectorAll('select')].indexOf(select);
                this.filters[index] = { value: select.value, label: normalize(select.selectedOptions[0].textContent) };
            }
            this.selected = [];
        },
        rowVisible(index) {
            const row = this.rows[index];
            if (!row || (this.kind === 'dashboard' && this.statusTab !== 0)) return false;
            if (this.kind === 'dashboard' && this.priorityFilter === 'danger' && index > 2) return false;
            if (this.kind === 'dashboard' && this.priorityFilter === 'overdue' && Number(row.cells[6]?.match(/\d+/)?.[0]) >= 21) return false;
            const query = normalize(this.query).replace(/^#/, '').replace(/sr-\d{4}-/g, 'sr-');
            if (!row.text.replace(/sr-\d{4}-/g, 'sr-').includes(query)) return false;
            if (this.kind === 'archive' && !this.matchesRange(row.cells[5])) return false;
            return Object.entries(this.filters).every(([key, filter]) => this.matchesFilter(row, Number(key), filter));
        },
        matchesFilter(row, index, { value, label }) {
            if (!value || value === 'all' || label.includes('semua')) return true;
            if (this.kind === 'dashboard') {
                const terms = {
                    fisik: 'fisik', verbal: 'verbal', dikucilkan: 'pengucilan', cyber: 'cyber', pelecehan: 'pelecehan', lainnya: 'lainnya',
                    siswa: 'siswa', ortu: 'orang tua', teman: 'teman korban', guru: 'guru', urgent: 'darurat', medium: 'medium', easy: 'easy',
                    ya: 'ya (', tidak: 'tidak',
                };
                if (index < 4) return (row.cells[[3, 4, 2, 7][index]] || '').includes(terms[value] || label);
                return this.matchesDate(row.cells[6], value);
            }
            if (this.kind === 'archive') {
                if (index === 0) return row.cells[4].includes(label);
                return row.cells[2].includes(label);
            }
            if (index === 0) return row.cells[1].includes(label.replace('staf ', '').replace('admin it & otomasi', 'otomasi'));
            if (index === 1) return this.matchesDate(row.cells[0], value, label);
            return true;
        },
        matchesDate(text, value, label = '') {
            // Dates are relative to the latest source sample, 24 October 2024.
            const day = Number(text.match(/\b\d{1,2}\b/)?.[0]);
            if (value === 'today' || label === 'hari ini') return day === 24;
            if (value === '7' || label.includes('7 hari')) return day >= 18;
            if (value === '30' || label.includes('30 hari') || label.includes('semester')) return true;
            if (value === 'custom') return (!this.dateFrom || `2024-10-${String(day).padStart(2, '0')}` >= this.dateFrom) && (!this.dateTo || `2024-10-${String(day).padStart(2, '0')}` <= this.dateTo);
            return true;
        },
        matchesRange(text) {
            const match = text.match(/(\d{1,2}) (\w+) (\d{4})/);
            if (!match) return true;
            const months = { jan: '01', feb: '02', mar: '03', apr: '04', mei: '05', jun: '06', jul: '07', ags: '08', sep: '09', okt: '10', nov: '11', des: '12' };
            const date = `${match[3]}-${months[match[2]] || '01'}-${match[1].padStart(2, '0')}`;
            return (!this.dateFrom || date >= this.dateFrom) && (!this.dateTo || date <= this.dateTo);
        },
        dateFilterOpen: false,
    }));

    Alpine.data('bkLearn', () => ({
        ...modalState(),
        title: '',
        content: '',
        visible: true,
        audience: 'siswa',
        isEditing: false,
        editingId: null,
        editingCode: '',
        updateUrl: '',
        filterAudience: 'all',
        filterStatus: 'all',
        sortBy: 'newest',
        searchQuery: '',
        previewTitle: '',
        previewContent: '',
        previewCode: '',
        previewAudience: '',
        previewAuthor: '',
        previewDate: '',
        toastMessage: '',
        showToast: false,

        init() {
            const titleEl = document.getElementById('post-title');
            const contentEl = document.getElementById('post-content');
            if (titleEl && !this.title) this.title = titleEl.value;
            if (contentEl && !this.content) this.content = contentEl.value;
        },

        newAnnouncement() {
            this.isEditing = false;
            this.editingId = null;
            this.editingCode = '';
            this.updateUrl = '';
            this.title = '';
            this.content = '';
            this.visible = true;
            this.audience = 'siswa';
            const titleEl = document.getElementById('post-title');
            if (titleEl) {
                titleEl.focus();
                titleEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },

        resetAnnouncement() {
            this.newAnnouncement();
        },

        editItem(item) {
            this.isEditing = true;
            this.editingId = item.id;
            this.editingCode = item.code;
            this.updateUrl = item.update_url;
            this.title = item.title;
            this.content = item.content;
            this.visible = Boolean(item.is_visible);
            this.audience = item.audience;
            const titleEl = document.getElementById('post-title');
            if (titleEl) {
                titleEl.focus();
                titleEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },

        previewItem(item) {
            this.previewTitle = item.title;
            this.previewContent = item.content;
            this.previewCode = item.code;
            this.previewAudience = item.audience_label;
            this.previewAuthor = item.author_name;
            this.previewDate = item.date;
            this.openModal('text-preview');
        },

        async toggleItemVisibility(item, event) {
            const checkbox = event.target;
            const originalState = !checkbox.checked;
            try {
                const response = await fetch(item.toggle_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                });
                const data = await response.json();
                if (data.success) {
                    item.is_visible = data.is_visible;
                    this.notify(data.message);
                } else {
                    checkbox.checked = originalState;
                    alert('Gagal memperbarui status tayang materi.');
                }
            } catch (err) {
                checkbox.checked = originalState;
                console.error(err);
            }
        },

        notify(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3500);
        },

        matchesFilter(item) {
            if (this.filterAudience !== 'all' && item.audience !== this.filterAudience) {
                return false;
            }
            if (this.filterStatus === 'active' && !item.is_visible) {
                return false;
            }
            if (this.filterStatus === 'hidden' && item.is_visible) {
                return false;
            }
            if (this.searchQuery.trim()) {
                const q = this.searchQuery.toLowerCase();
                const titleMatch = (item.title || '').toLowerCase().includes(q);
                const contentMatch = (item.content || '').toLowerCase().includes(q);
                const codeMatch = (item.code || '').toLowerCase().includes(q);
                if (!titleMatch && !contentMatch && !codeMatch) return false;
            }
            return true;
        }
    }));

    Alpine.data('bkProfile', () => ({
        ...modalState(),
        togglePassword(button) {
            const input = button.parentElement.querySelector('input');
            input.type = input.type === 'password' ? 'text' : 'password';
            button.querySelector('.material-symbols-outlined').textContent = input.type === 'password' ? 'visibility' : 'visibility_off';
            button.setAttribute('aria-pressed', String(input.type === 'text'));
        },
        submitPassword(event) {
            const password = document.getElementById('newPass');
            const confirmation = document.getElementById('confPass');
            confirmation.setCustomValidity(password.value === confirmation.value ? '' : 'Konfirmasi kata sandi harus sama.');
            if (event.currentTarget.reportValidity()) this.unavailable('Perubahan kata sandi');
        },
        submitWalasPassword(event) {
            const password = document.getElementById('newPassword');
            const confirmation = document.getElementById('confirmPassword');
            confirmation.setCustomValidity(password.value === confirmation.value ? '' : 'Konfirmasi kata sandi harus sama.');
            if (event.currentTarget.reportValidity()) this.unavailable('Perubahan kata sandi');
        },
    }));

    Alpine.data('bkReport', () => ({
        ...modalState(),
        urgency: 'Darurat',
        summary: '',
        openForward() {
            this.summary = '';
            this.openModal('forward-modal');
        },
    }));

    Alpine.data('kesiswaanReport', () => ({ ...modalState() }));

    Alpine.data('kesiswaanTable', (archive = false) => ({
        ...modalState(),
        archive,
        query: '',
        status: 'belum_dibaca',
        priorityFilter: '',
        filters: { type: '', role: '', urgency: '', contact: '', reason: '' },
        period: '',
        dateFrom: '',
        dateTo: '',
        selected: [],
        rows: [],
        init() {
            this.rows = [...this.$root.querySelectorAll('[data-report-row]')].map(row => ({
                ...row.dataset,
                text: normalize(row.textContent).replace(/sr-\d{4}-/g, 'sr-'),
            }));
        },
        rowVisible(index) {
            const row = this.rows[index];
            if (!row) return false;
            if (!this.archive && this.priorityFilter === 'danger' && row.danger !== 'true') return false;
            if (!this.archive && this.priorityFilter === 'overdue' && row.date >= '2024-10-21') return false;
            if (!this.archive && (this.status === 'handling' ? !['dibaca', 'ditindaklanjuti'].includes(row.status) : row.status !== this.status)) return false;
            const query = normalize(this.query).replace(/^#/, '').replace(/sr-\d{4}-/g, 'sr-');
            if (!row.text.includes(query)) return false;
            if (!Object.entries(this.filters).every(([key, value]) => !value || row[key] === value)) return false;
            if (this.period === 'custom') return (!this.dateFrom || row.date >= this.dateFrom) && (!this.dateTo || row.date <= this.dateTo);
            return !this.period || (row.date >= this.period && row.date <= '2024-10-24');
        },
        get visibleRows() { return this.rows.filter((row, index) => this.rowVisible(index)); },
        get visibleCount() { return this.visibleRows.length; },
        get allSelected() { return this.visibleCount > 0 && this.visibleRows.every(row => this.selected.includes(row.id)); },
        selectVisible(checked) { this.selected = checked ? this.visibleRows.map(row => row.id) : []; },
    }));

    Alpine.data('walasDashboard', () => ({
        filter: 'unread',
        query: '',
        rows: [],
        init() {
            this.rows = [...this.$root.querySelectorAll('[data-summary-card]')].map(card => ({
                status: card.dataset.status,
                text: normalize(card.textContent),
            }));
        },
        visible(index) {
            const row = this.rows[index];
            return !!row && (this.filter === 'all' || row.status === this.filter) && row.text.includes(normalize(this.query));
        },
        get visibleCount() { return this.rows.filter((row, index) => this.visible(index)).length; },
    }));

    Alpine.data('bkStatistics', () => ({
        goToLocations(url) { navigateTo(url); },
    }));
}
