import { manageRows } from './bk-data';
import { QRCode } from './qrcode';
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

    Alpine.data('bkManage', (initialData = {}) => ({
        ...modalState(),
        tab: 'jenis',
        categories: initialData.categories ? [...initialData.categories] : structuredClone(manageRows.categories),
        locations: initialData.locations ? [...initialData.locations] : structuredClone(manageRows.locations),
        staffMembers: initialData.staffMembers ? [...initialData.staffMembers] : [],
        qrTotalScans: initialData.qrTotalScans ?? 0,
        reportUrl: initialData.reportUrl || (window.location.origin + '/s/lapor'),
        qrScanUrl: initialData.qrScanUrl || (window.location.origin + '/s/qr'),
        qrSvgHtml: '',
        dragging: null,
        dropTarget: '',
        orderMessage: '',
        editing: false,
        editingId: null,
        categoryLabel: '',
        categoryActive: true,
        categoryPermanent: false,
        categoryVisibleKesiswaan: true,
        locationLabel: '',
        locationActive: true,
        staffId: null,
        staffName: '',
        staffEmail: '',
        staffRole: 'kesiswaan',
        staffClass: '7A',
        staffStatus: 'pending',
        qrQuery: '',
        staffQuery: '',
        saving: false,

        get csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        },

        init() {
            const hash = window.location.hash.slice(1);
            if (['jenis', 'lokasi', 'qr', 'staf'].includes(hash)) this.tab = hash;
            window.addEventListener('hashchange', () => {
                const h = window.location.hash.slice(1);
                if (['jenis', 'lokasi', 'qr', 'staf'].includes(h)) this.tab = h;
            });
            this.renderQrCode();
            window.addEventListener('focus', () => {
                if (this.tab === 'qr') this.fetchQrStats();
            });
            setInterval(() => {
                if (this.tab === 'qr' && !document.hidden) this.fetchQrStats();
            }, 6000);
        },

        setTab(tab) {
            this.tab = tab;
            window.location.hash = tab;
        },

        showNotice(title, message) {
            window.dispatchEvent(new CustomEvent('auth-notice', { detail: { title, message } }));
        },

        // --- Drag & Drop / Reordering ---
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
        async reorder(list, from, to) {
            if (from < 0 || to < 0 || to >= this[list].length || from === to || this[list][from].permanent || this[list][to].permanent) return;
            const rows = [...this[list]];
            const [item] = rows.splice(from, 1);
            rows.splice(to, 0, item);
            this[list] = rows;
            this.orderMessage = `${item.label} dipindahkan ke urutan ${to + 1}.`;

            try {
                const endpoint = list === 'categories' ? '/bk/manage/categories/reorder' : '/bk/manage/locations/reorder';
                await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ order: this[list].map(r => r.id) }),
                });
            } catch (e) {
                console.error('Failed to sync reorder', e);
            }
        },
        endDrag() {
            this.dragging = null;
            this.dropTarget = '';
        },

        // --- Category CRUD ---
        openCategory(item = null) {
            this.editing = !!item;
            this.editingId = item ? item.id : null;
            this.categoryLabel = item?.label || '';
            this.categoryActive = item?.active ?? true;
            this.categoryPermanent = item?.permanent ?? false;
            this.categoryVisibleKesiswaan = item?.visible_to_kesiswaan ?? true;
            this.openModal('modal-kategori');
        },
        async saveCategory() {
            if (!this.categoryLabel.trim()) {
                this.showNotice('Validasi Gagal', 'Nama jenis kejadian wajib diisi.');
                return;
            }
            this.saving = true;
            try {
                const url = this.editing ? `/bk/manage/categories/${this.editingId}` : '/bk/manage/categories';
                const method = this.editing ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        label: this.categoryLabel.trim(),
                        active: this.categoryActive,
                        visible_to_kesiswaan: this.categoryVisibleKesiswaan,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    if (this.editing) {
                        const idx = this.categories.findIndex(c => c.id === this.editingId);
                        if (idx !== -1) {
                            this.categories[idx].label = this.categoryLabel.trim();
                            this.categories[idx].active = this.categoryActive;
                            this.categories[idx].visible_to_kesiswaan = this.categoryVisibleKesiswaan;
                        }
                    } else if (data.category) {
                        this.categories.push(data.category);
                    }
                    this.closeModal();
                    this.showNotice('Berhasil', data.message || 'Kategori aduan berhasil disimpan.');
                } else {
                    this.showNotice('Gagal', data.message || 'Terjadi kesalahan saat menyimpan.');
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal menghubungkan ke server.');
            } finally {
                this.saving = false;
            }
        },
        async toggleCategory(item) {
            if (item.permanent) return;
            try {
                const res = await fetch(`/bk/manage/categories/${item.id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    item.active = data.active;
                    this.showNotice('Status Berubah', data.message);
                }
            } catch (err) {
                console.error(err);
            }
        },
        async deleteCategory(item) {
            if (item.permanent) {
                this.showNotice('Dilindungi', 'Kategori ini dilindungi sistem dan tidak dapat dihapus.');
                return;
            }
            if (!confirm(`Hapus kategori aduan "${item.label}"?`)) return;

            try {
                const res = await fetch(`/bk/manage/categories/${item.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.categories = this.categories.filter(c => c.id !== item.id);
                    this.showNotice('Berhasil Dihapus', data.message);
                } else {
                    this.showNotice('Gagal', data.message);
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal menghapus kategori.');
            }
        },

        // --- Location CRUD ---
        openLocation(item = null) {
            this.editing = !!item;
            this.editingId = item ? item.id : null;
            this.locationLabel = item?.label || '';
            this.openModal('modal-tambah-lokasi');
        },
        async saveLocation() {
            if (!this.locationLabel.trim()) {
                this.showNotice('Validasi Gagal', 'Nama titik lokasi wajib diisi.');
                return;
            }
            this.saving = true;
            try {
                const url = this.editing ? `/bk/manage/locations/${this.editingId}` : '/bk/manage/locations';
                const method = this.editing ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        label: this.locationLabel.trim(),
                        active: true,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    if (this.editing) {
                        const idx = this.locations.findIndex(l => l.id === this.editingId);
                        if (idx !== -1) this.locations[idx].label = this.locationLabel.trim();
                    } else if (data.location) {
                        this.locations.push(data.location);
                    }
                    this.closeModal();
                    this.showNotice('Berhasil', data.message || 'Titik lokasi berhasil disimpan.');
                } else {
                    this.showNotice('Gagal', data.message || 'Terjadi kesalahan saat menyimpan.');
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal menghubungkan ke server.');
            } finally {
                this.saving = false;
            }
        },
        async toggleLocation(item) {
            try {
                const res = await fetch(`/bk/manage/locations/${item.id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    item.active = data.active;
                    this.showNotice('Status Berubah', data.message);
                }
            } catch (err) {
                console.error(err);
            }
        },
        async deleteLocation(item) {
            if (!confirm(`Hapus titik lokasi "${item.label}"?`)) return;
            try {
                const res = await fetch(`/bk/manage/locations/${item.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.locations = this.locations.filter(l => l.id !== item.id);
                    this.showNotice('Berhasil Dihapus', data.message);
                } else {
                    this.showNotice('Gagal', data.message);
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal menghapus lokasi.');
            }
        },

        // --- Staff CRUD ---
        openInvite() {
            this.staffId = null;
            this.staffName = '';
            this.staffEmail = '';
            this.staffRole = 'kesiswaan';
            this.staffClass = '7A';
            this.staffStatus = 'pending';
            this.openModal('modal-undang-staf');
        },
        async submitInvite() {
            if (!this.staffEmail.trim() || !this.staffEmail.includes('@')) {
                this.showNotice('Validasi Gagal', 'Alamat email staf wajib diisi dengan benar.');
                return;
            }
            this.saving = true;
            try {
                const res = await fetch('/bk/manage/staff', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        name: this.staffName.trim(),
                        email: this.staffEmail.trim(),
                        role: this.staffRole,
                        class_name: this.staffRole === 'walikelas' ? this.staffClass : null,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    if (data.staff) {
                        this.staffMembers.unshift(data.staff);
                    }
                    this.closeModal();
                    this.showNotice('Undangan Terkirim', data.message);
                } else {
                    this.showNotice('Gagal Mengirim', data.message || 'Terjadi kesalahan.');
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal mengirim undangan staf.');
            } finally {
                this.saving = false;
            }
        },
        openEditStaff(item) {
            this.staffId = item.id;
            this.staffName = item.name || '';
            this.staffEmail = item.email || '';
            this.staffRole = item.role || 'kesiswaan';
            this.staffClass = item.class_name || '7A';
            this.staffStatus = item.status || 'pending';
            this.openModal('modal-edit-staf');
        },
        async saveStaff() {
            if (!this.staffName.trim() || !this.staffEmail.trim()) {
                this.showNotice('Validasi Gagal', 'Nama lengkap dan email wajib diisi.');
                return;
            }
            this.saving = true;
            try {
                const res = await fetch(`/bk/manage/staff/${this.staffId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        name: this.staffName.trim(),
                        email: this.staffEmail.trim(),
                        role: this.staffRole,
                        class_name: this.staffRole === 'walikelas' ? this.staffClass : null,
                        status: this.staffStatus,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    const idx = this.staffMembers.findIndex(s => s.id === this.staffId);
                    if (idx !== -1 && data.staff) {
                        this.staffMembers[idx] = data.staff;
                    }
                    this.closeModal();
                    this.showNotice('Berhasil Diperbarui', data.message);
                } else {
                    this.showNotice('Gagal', data.message || 'Terjadi kesalahan.');
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal memperbarui staf.');
            } finally {
                this.saving = false;
            }
        },
        openDeleteStaff(item) {
            this.staffId = item.id;
            this.staffName = item.name || 'Staf';
            this.openModal('modal-hapus-staf');
        },
        async confirmDeleteStaff() {
            this.saving = true;
            try {
                const res = await fetch(`/bk/manage/staff/${this.staffId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.staffMembers = this.staffMembers.filter(s => s.id !== this.staffId);
                    this.closeModal();
                    this.showNotice('Akses Dicabut', data.message);
                } else {
                    this.showNotice('Gagal', data.message);
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal mencabut akses staf.');
            } finally {
                this.saving = false;
            }
        },
        async resendInvite(item) {
            if (this.saving) return;
            this.saving = true;
            try {
                const res = await fetch(`/bk/manage/staff/${item.id}/resend`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.showNotice('Undangan Terkirim', data.message);
                } else {
                    this.showNotice('Gagal Mengirim', data.message || 'Gagal mengirim ulang undangan.');
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal menghubungi server.');
            } finally {
                this.saving = false;
            }
        },

        // --- QR Code & Downloads ---
        renderQrCode() {
            try {
                const qr = new QRCode(0, 'M');
                qr.addData(this.qrScanUrl);
                qr.make();
                this.qrSvgHtml = qr.createSvg(220, 4);
            } catch (e) {
                console.error('Failed to generate QR', e);
            }
        },
        async fetchQrStats() {
            try {
                const res = await fetch('/bk/manage/qr/stats');
                const data = await res.json();
                if (data.success && typeof data.total_scans === 'number') {
                    this.qrTotalScans = data.total_scans;
                }
            } catch (e) {
                // silent
            }
        },
        testScanQr() {
            window.open(this.qrScanUrl, '_blank');
            setTimeout(() => this.fetchQrStats(), 1000);
            this.showNotice('Memindai QR', 'Simulasi pemindaian QR sedang berjalan di tab baru.');
        },
        copyReportUrl() {
            navigator.clipboard.writeText(this.reportUrl).then(() => {
                this.showNotice('Tautan Tersalin', 'Tautan formulir pelaporan siswa telah disalin ke papan klip.');
            }).catch(() => {
                this.showNotice('Belum tersalin', 'Pilih dan salin tautan secara manual.');
            });
        },
        downloadQrPng() {
            const svg = document.querySelector('#panel-qr svg');
            if (!svg) {
                this.showNotice('Gagal', 'Elemen gambar QR tidak ditemukan.');
                return;
            }
            try {
                const svgData = new XMLSerializer().serializeToString(svg);
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();
                canvas.width = 1200;
                canvas.height = 1200;
                const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
                const url = URL.createObjectURL(svgBlob);
                img.onload = () => {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, 1200, 1200);
                    ctx.drawImage(img, 60, 60, 1080, 1080);
                    URL.revokeObjectURL(url);
                    const a = document.createElement('a');
                    a.download = 'QR-Pengaduan-Sekolah.png';
                    a.href = canvas.toDataURL('image/png');
                    a.click();
                    this.showNotice('Berhasil', 'Gambar QR Code resmi (PNG 300 DPI) berhasil diunduh.');
                };
                img.src = url;
            } catch (e) {
                console.error(e);
                this.showNotice('Gagal', 'Gagal membuat gambar PNG dari QR.');
            }
        },
        openPosterA4() {
            window.open('/bk/manage/poster', '_blank');
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

    Alpine.data('walasDashboard', (config = {}) => ({
        filter: 'unread',
        query: '',
        rows: [],
        qrModalOpen: false,
        qrSvgHtml: '',
        reportUrl: config.reportUrl || (window.location.origin + '/s/lapor'),
        qrScanUrl: config.qrScanUrl || (window.location.origin + '/s/qr'),
        className: config.className || '7B',

        init() {
            this.refreshRows();
            this.renderQrCode();
            if (window.location.hash === '#qr') {
                this.qrModalOpen = true;
            }
        },
        refreshRows() {
            this.rows = [...this.$root.querySelectorAll('[data-summary-card]')].map(card => ({
                id: card.dataset.id,
                status: card.dataset.status,
                text: normalize(card.textContent),
            }));
        },
        visible(index) {
            const row = this.rows[index];
            return !!row && (this.filter === 'all' || row.status === this.filter) && row.text.includes(normalize(this.query));
        },
        get visibleCount() {
            return this.rows.filter((row, index) => this.visible(index)).length;
        },
        renderQrCode() {
            try {
                if (typeof QRCode !== 'undefined') {
                    const qr = new QRCode(0, 'M');
                    qr.addData(this.qrScanUrl);
                    qr.make();
                    this.qrSvgHtml = qr.createSvg(200, 4);
                }
            } catch (e) {
                console.error('Failed to generate Walas QR', e);
            }
        },
        copyReportUrl() {
            navigator.clipboard.writeText(this.reportUrl).then(() => {
                window.dispatchEvent(new CustomEvent('auth-notice', {
                    detail: { title: 'Tautan Tersalin', message: 'Tautan aduan siswa kelas berhasil disalin.' }
                }));
            }).catch(() => {
                window.dispatchEvent(new CustomEvent('auth-notice', {
                    detail: { title: 'Belum tersalin', message: 'Pilih dan salin tautan secara manual.' }
                }));
            });
        },
        downloadQrPng() {
            const svg = document.querySelector('#walas-qr-svg svg');
            if (!svg) {
                window.dispatchEvent(new CustomEvent('auth-notice', {
                    detail: { title: 'Gagal', message: 'Elemen gambar QR tidak ditemukan.' }
                }));
                return;
            }
            try {
                const svgData = new XMLSerializer().serializeToString(svg);
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();
                canvas.width = 1200;
                canvas.height = 1200;
                const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
                const url = URL.createObjectURL(svgBlob);
                img.onload = () => {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, 1200, 1200);
                    ctx.drawImage(img, 60, 60, 1080, 1080);
                    URL.revokeObjectURL(url);
                    const a = document.createElement('a');
                    a.download = `QR-Pengaduan-Kelas-${this.className}.png`;
                    a.href = canvas.toDataURL('image/png');
                    a.click();
                };
                img.src = url;
            } catch (err) {
                console.error(err);
            }
        },
        async quickToggleRead(id, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            try {
                const res = await fetch(`/wali-kelas/ringkasan/${id}/toggle-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        }
    }));

    Alpine.data('bkStatistics', () => ({
        goToLocations(url) { navigateTo(url); },
    }));
}
