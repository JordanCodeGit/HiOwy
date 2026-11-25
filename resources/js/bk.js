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
        showNotice(title, message) {
            window.dispatchEvent(new CustomEvent('auth-notice', { detail: { title, message } }));
        },
        unavailable(feature = 'Fitur') {
            window.dispatchEvent(new CustomEvent('auth-notice', {
                detail: {
                    title: 'Pratinjau Frontend',
                    message: `${feature} disimulasikan pada prototipe antarmuka ini.`,
                },
            }));
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
        reportUrl: initialData.reportUrl || (window.location.origin + '/s/lapor'),
        qrScanUrl: initialData.qrScanUrl || (window.location.origin + '/s/qr'),
        qrSvgHtml: '',
        posterQrSvgHtml: '',
        editing: false,
        editingId: null,
        deleteTargetId: null,
        deleteTargetLabel: '',
        categoryLabel: '',
        categoryPermendikbud: 'Perundungan / Bullying (Permendikbudristek 46/2023)',
        categoryPermanent: false,
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
        trainingMode: Boolean(initialData.trainingMode),
        nlpEnabled: Boolean(initialData.nlpEnabled),
        readingDayStart: initialData.readingDayStart || 'Senin',
        readingDayEnd: initialData.readingDayEnd || 'Jumat',
        readingTimeStart: initialData.readingTimeStart || '07:00',
        readingTimeEnd: initialData.readingTimeEnd || '15:00',
        logoPreviewUrl: initialData.schoolLogoUrl || '',
        logoFileName: '',
        saving: false,

        get csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        },

        get formattedReadingHours() {
            const dayPart = this.readingDayStart === this.readingDayEnd
                ? this.readingDayStart
                : `${this.readingDayStart}–${this.readingDayEnd}`;
            const tStart = (this.readingTimeStart || '07:00').replace(':', '.');
            const tEnd = (this.readingTimeEnd || '15:00').replace(':', '.');
            return `${dayPart}, ${tStart}–${tEnd} WIB`;
        },

        onLogoSelected(event) {
            const file = event?.target?.files?.[0];
            if (!file) return;
            this.logoFileName = file.name;
            this.logoPreviewUrl = URL.createObjectURL(file);
        },

        init() {
            const hash = window.location.hash.slice(1);
            if (['jenis', 'lokasi', 'qr', 'staf', 'sekolah'].includes(hash)) this.tab = hash;
            window.addEventListener('hashchange', () => {
                const h = window.location.hash.slice(1);
                if (['jenis', 'lokasi', 'qr', 'staf', 'sekolah'].includes(h)) this.tab = h;
            });
            this.renderQrCode();
        },

        setTab(tab) {
            this.tab = tab;
            window.location.hash = tab;
        },

        showNotice(title, message) {
            window.dispatchEvent(new CustomEvent('auth-notice', { detail: { title, message } }));
        },

        // --- Category CRUD ---
        openCategory(item = null) {
            this.editing = !!item;
            this.editingId = item ? item.id : null;
            this.categoryLabel = item?.label || '';
            this.categoryPermendikbud = item?.permendikbud_type || 'Perundungan / Bullying (Permendikbudristek 46/2023)';
            this.categoryPermanent = item?.permanent ?? false;
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
                        permendikbud_type: this.categoryPermendikbud,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    if (this.editing) {
                        const idx = this.categories.findIndex(c => c.id === this.editingId);
                        if (idx !== -1) {
                            this.categories[idx] = data.category || {
                                ...this.categories[idx],
                                label: this.categoryLabel.trim(),
                                permendikbud_type: this.categoryPermendikbud,
                                active: true,
                            };
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
        openDeleteCategory(item) {
            if (item.permanent) {
                this.showNotice('Dilindungi', 'Kategori ini dilindungi sistem dan tidak dapat dihapus.');
                return;
            }
            this.deleteTargetId = item.id;
            this.deleteTargetLabel = item.label || 'Kategori';
            this.openModal('modal-hapus-kategori');
        },
        async confirmDeleteCategory() {
            if (!this.deleteTargetId) return;
            this.saving = true;
            try {
                const res = await fetch(`/bk/manage/categories/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.categories = this.categories.filter(c => c.id !== this.deleteTargetId);
                    this.closeModal();
                    this.showNotice('Berhasil Dihapus', data.message);
                } else {
                    this.showNotice('Gagal', data.message);
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal menghapus kategori.');
            } finally {
                this.saving = false;
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
        openDeleteLocation(item) {
            if (item.permanent) {
                this.showNotice('Dilindungi', 'Lokasi ini dilindungi sistem dan tidak dapat dihapus.');
                return;
            }
            this.deleteTargetId = item.id;
            this.deleteTargetLabel = item.label || 'Lokasi';
            this.openModal('modal-hapus-lokasi');
        },
        async confirmDeleteLocation() {
            if (!this.deleteTargetId) return;
            this.saving = true;
            try {
                const res = await fetch(`/bk/manage/locations/${this.deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.locations = this.locations.filter(l => l.id !== this.deleteTargetId);
                    this.closeModal();
                    this.showNotice('Berhasil Dihapus', data.message);
                } else {
                    this.showNotice('Gagal', data.message);
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal menghapus lokasi.');
            } finally {
                this.saving = false;
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
        async resetStaffAccess(item) {
            if (this.saving) return;
            if (!confirm(`Reset kata sandi & 2FA untuk "${item.name}" lalu kirim tautan aktivasi baru ke ${item.email}?`)) return;
            this.saving = true;
            try {
                const res = await fetch(`/bk/manage/staff/${item.id}/reset-access`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    item.status = 'pending';
                    this.showNotice('Akses Direset', data.message);
                } else {
                    this.showNotice('Gagal Mereset', data.message || 'Gagal mereset akun staf.');
                }
            } catch (err) {
                console.error(err);
                this.showNotice('Terjadi Kesalahan', 'Gagal menghubungi server.');
            } finally {
                this.saving = false;
            }
        },

        async toggleSetting(key, value) {
            const prev = this[key === 'training_mode' ? 'trainingMode' : 'nlpEnabled'];
            if (key === 'training_mode') this.trainingMode = Boolean(value);
            if (key === 'nlp_enabled') this.nlpEnabled = Boolean(value);
            try {
                const res = await fetch('/bk/manage/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ [key]: Boolean(value) }),
                });
                const data = await res.json();
                if (data.success) {
                    this.trainingMode = Boolean(data.training_mode);
                    this.nlpEnabled = Boolean(data.nlp_enabled);
                    this.showNotice('Tersimpan', data.message);
                } else {
                    if (key === 'training_mode') this.trainingMode = prev;
                    if (key === 'nlp_enabled') this.nlpEnabled = prev;
                    this.showNotice('Gagal', data.message || 'Gagal memperbarui pengaturan.');
                }
            } catch (err) {
                console.error(err);
                if (key === 'training_mode') this.trainingMode = prev;
                if (key === 'nlp_enabled') this.nlpEnabled = prev;
                this.showNotice('Error', 'Gagal menghubungi server.');
            }
        },

        // --- QR Code & Downloads ---
        renderQrCode() {
            try {
                const qr = new QRCode(0, 'M');
                qr.addData(this.reportUrl || this.qrScanUrl);
                qr.make();
                this.qrSvgHtml = qr.createSvg(208, 4);
                this.posterQrSvgHtml = qr.createSvg(124, 3);
            } catch (e) {
                console.error('Failed to generate QR', e);
            }
        },
        copyReportUrl() {
            navigator.clipboard.writeText(this.reportUrl).then(() => {
                this.showNotice('Tautan Tersalin', 'Tautan formulir pelaporan siswa telah disalin ke papan klip.');
            }).catch(() => {
                this.showNotice('Belum tersalin', 'Pilih dan salin tautan secara manual.');
            });
        },
        downloadQrPng() {
            const svg = document.querySelector('#qr-main-svg svg') || document.querySelector('#panel-qr svg');
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
        categoryFilter: '',
        roleFilter: '',
        urgencyFilter: '',
        contactFilter: '',
        timeFilter: '',
        filters: {},
        selected: [],
        rows: [],
        currentPage: 1,
        pageSize: 10,
        dateFrom: '',
        dateTo: '',
        dateFilterOpen: false,

        init() {
            this.rows = [...this.$root.querySelectorAll('[data-preview-row]')].map((row, index) => ({
                index,
                id: row.dataset.id || String(index),
                status: row.dataset.status || 'unread',
                urgency: row.dataset.urgency || '',
                category: row.dataset.category || '',
                categoryLabel: (row.dataset.categoryLabel || '').toLowerCase(),
                role: row.dataset.role || '',
                contact: row.dataset.contact || '',
                stale: row.dataset.stale === '1',
                date: row.dataset.date || '',
                text: normalize(row.textContent).replace(/sr-\d{4}-/g, 'sr-'),
                cells: [...row.cells].map(cell => normalize(cell.textContent)),
            }));
        },
        get filteredRows() {
            return this.rows.filter(row => {
                if (this.kind === 'dashboard') {
                    // Status Tab: 0 = unread, 1 = in_progress, 2 = resolved
                    if (this.statusTab === 0 && row.status !== 'unread') return false;
                    if (this.statusTab === 1 && row.status !== 'in_progress') return false;
                    if (this.statusTab === 2 && row.status !== 'resolved') return false;

                    // Priority filters from top cards
                    if (this.priorityFilter === 'danger' && row.urgency !== 'urgent') return false;
                    if (this.priorityFilter === 'overdue' && !row.stale) return false;

                    // Dropdown filters
                    if (this.categoryFilter && row.category !== this.categoryFilter && row.categoryLabel !== this.categoryFilter.toLowerCase()) return false;
                    if (this.roleFilter && row.role !== this.roleFilter) return false;
                    if (this.urgencyFilter && row.urgency !== this.urgencyFilter) return false;
                    if (this.contactFilter && row.contact !== this.contactFilter) return false;

                    // Time filter
                    if (this.timeFilter && row.date) {
                        const now = new Date();
                        const rowDate = new Date(row.date);
                        const diffDays = Math.floor((now - rowDate) / (1000 * 60 * 60 * 24));
                        if (this.timeFilter === '7' && diffDays > 7) return false;
                        if (this.timeFilter === '30' && diffDays > 30) return false;
                    }
                }

                // Query search
                if (this.query) {
                    const q = normalize(this.query).replace(/^#/, '').replace(/sr-\d{4}-/g, 'sr-');
                    if (!row.text.includes(q)) return false;
                }

                // Custom date range
                if (this.dateFrom && row.date && row.date < this.dateFrom) return false;
                if (this.dateTo && row.date && row.date > this.dateTo) return false;

                // Legacy filters fallback for archive / other pages
                if (this.kind !== 'dashboard') {
                    if (this.kind === 'archive' && !this.matchesRange(row.cells[5])) return false;
                    if (!Object.entries(this.filters).every(([key, filter]) => this.matchesFilter(row, Number(key), filter))) return false;
                }

                return true;
            });
        },
        get filteredCount() {
            return this.filteredRows.length;
        },
        get visibleCount() {
            return this.filteredCount;
        },
        get totalPages() {
            return Math.ceil(this.filteredRows.length / this.pageSize) || 1;
        },
        get pageStart() {
            return this.filteredRows.length === 0 ? 0 : (this.currentPage - 1) * this.pageSize + 1;
        },
        get pageEnd() {
            return Math.min(this.currentPage * this.pageSize, this.filteredRows.length);
        },
        get paginatedRows() {
            const start = (this.currentPage - 1) * this.pageSize;
            return this.filteredRows.slice(start, start + this.pageSize);
        },
        rowVisible(index) {
            if (this.kind === 'dashboard') {
                return this.paginatedRows.some(row => row.index === index);
            }
            const row = this.rows[index];
            if (!row) return false;
            const query = normalize(this.query).replace(/^#/, '').replace(/sr-\d{4}-/g, 'sr-');
            if (!row.text.includes(query)) return false;
            return Object.entries(this.filters).every(([key, filter]) => this.matchesFilter(row, Number(key), filter));
        },
        pageSizeChanged(event) {
            this.pageSize = Number.parseInt(event.target.value, 10) || 10;
            this.currentPage = 1;
            this.selected = [];
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.selected = [];
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.selected = [];
            }
        },
        resetFilters() {
            this.query = '';
            this.priorityFilter = '';
            this.categoryFilter = '';
            this.roleFilter = '';
            this.urgencyFilter = '';
            this.contactFilter = '';
            this.timeFilter = '';
            this.dateFrom = '';
            this.dateTo = '';
            this.dateFilterOpen = false;
            this.filters = {};
            this.currentPage = 1;
            this.selected = [];
            this.$root.querySelectorAll('select').forEach(select => {
                if (!select.options[0]?.textContent.includes('/ halaman')) {
                    select.selectedIndex = 0;
                }
            });
        },
        get allSelected() {
            const visibleIds = this.paginatedRows.map(row => String(row.id));
            return visibleIds.length > 0 && visibleIds.every(id => this.selected.includes(id));
        },
        selectVisible(checked) {
            const visibleIds = this.paginatedRows.map(row => String(row.id));
            if (checked) {
                this.selected = Array.from(new Set([...this.selected, ...visibleIds]));
            } else {
                this.selected = this.selected.filter(id => !visibleIds.includes(id));
            }
        },
        filterChanged(event) {
            const select = event.target;
            if (select.options[0]?.textContent.includes('/ halaman')) {
                this.pageSize = Number.parseInt(select.selectedOptions[0].textContent, 10) || 10;
                this.currentPage = 1;
            } else {
                const index = [...this.$root.querySelectorAll('select')].indexOf(select);
                this.filters[index] = { value: select.value, label: normalize(select.selectedOptions[0].textContent) };
            }
            this.selected = [];
        },
        matchesFilter(row, index, { value, label }) {
            if (!value || value === 'all' || label.includes('semua')) return true;
            if (this.kind === 'archive') {
                if (index === 0) return row.cells[4].includes(label);
                return row.cells[2].includes(label);
            }
            if (index === 0) return row.cells[1].includes(label.replace('staf ', '').replace('admin it & otomasi', 'otomasi'));
            if (index === 1) return this.matchesDate(row.cells[0], value, label);
            return true;
        },
        matchesDate(text, value, label = '') {
            const months = { jan: '01', feb: '02', mar: '03', apr: '04', mei: '05', jun: '06', jul: '07', ags: '08', agu: '08', sep: '09', okt: '10', nov: '11', des: '12' };
            const now = new Date();
            const todayIso = now.toISOString().slice(0, 10);
            let rowIso = '';

            if (text.includes('hari ini')) {
                rowIso = todayIso;
            } else if (text.includes('kemarin')) {
                rowIso = new Date(now.getTime() - 86400000).toISOString().slice(0, 10);
            } else {
                const match = text.match(/(\d{1,2})\s+([a-z]{3,})\s+(\d{4})/i);
                if (match) {
                    const mKey = match[2].slice(0, 3).toLowerCase();
                    rowIso = `${match[3]}-${months[mKey] || '01'}-${String(match[1]).padStart(2, '0')}`;
                }
            }

            if (!rowIso) return true;
            const diffDays = Math.floor((new Date(todayIso) - new Date(rowIso)) / 86400000);

            if (value === 'today' || label === 'hari ini') return rowIso === todayIso;
            if (value === '7' || value === '7d' || label.includes('7 hari')) return diffDays <= 7;
            if (value === '30' || value === '30d' || label.includes('30 hari') || label.includes('semester')) return diffDays <= 180;
            if (value === 'custom') return (!this.dateFrom || rowIso >= this.dateFrom) && (!this.dateTo || rowIso <= this.dateTo);
            return true;
        },
        matchesRange(text) {
            const match = text.match(/(\d{1,2}) (\w+) (\d{4})/);
            if (!match) return true;
            const months = { jan: '01', feb: '02', mar: '03', apr: '04', mei: '05', jun: '06', jul: '07', ags: '08', sep: '09', okt: '10', nov: '11', des: '12' };
            const date = `${match[3]}-${months[match[2]] || '01'}-${match[1].padStart(2, '0')}`;
            return (!this.dateFrom || date >= this.dateFrom) && (!this.dateTo || date <= this.dateTo);
        },
        async submitBulkIseng() {
            if (this.selected.length === 0) return;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch('/bk/reports/bulk-iseng', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ ids: this.selected }),
                });
                const data = await res.json();
                if (data.success) {
                    this.closeModal();
                    this.showNotice('Berhasil', data.message || 'Laporan berhasil ditandai sebagai iseng.');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal menandai laporan.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan sistem saat menandai laporan.');
            }
        },
        async submitBulkUnarchive() {
            if (this.selected.length === 0) return;
            if (!confirm(`Keluarkan ${this.selected.length} laporan terpilih dari arsip ke antrean aktif?`)) return;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch('/bk/reports/bulk-unarchive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ ids: this.selected.map(Number) }),
                });
                const data = await res.json();
                if (data.success) {
                    this.showNotice('Berhasil', data.message || 'Laporan berhasil dikeluarkan dari arsip.');
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal memulihkan laporan.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan saat memulihkan laporan dari arsip.');
            }
        },
        async unarchiveSingle(id) {
            if (!confirm('Keluarkan laporan ini dari arsip kembali ke antrean aktif?')) return;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/bk/reports/${id}/unarchive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.showNotice('Berhasil', data.message || 'Laporan berhasil dikeluarkan dari arsip.');
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal memulihkan laporan.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan saat memulihkan laporan.');
            }
        },
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

    Alpine.data('bkReport', (config = {}) => ({
        ...modalState(),
        reportId: config.reportId || null,
        urgency: config.urgency || 'Darurat',
        caseStatus: config.caseStatus || 'dibaca',
        categoryChoice: config.categoryChoice || '',
        caseHandler: config.caseHandler || '',
        summary: '',
        forwardToUserId: config.firstWalasId || '',
        counselorReply: '',
        internalNote: '',
        archiveReason: 'selesai',
        loading: false,

        openForward() {
            this.summary = '';
            this.openModal('forward-modal');
        },

        async updateCase(id = null) {
            const reportId = id || this.reportId;
            if (!reportId) return;
            this.loading = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/bk/reports/${reportId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        status: this.caseStatus,
                        urgency: this.urgency,
                        assigned_counselor: this.caseHandler,
                        category: this.categoryChoice || undefined,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.showNotice('Berhasil', data.message || 'Status dan penugasan laporan berhasil diperbarui.');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal memperbarui status kasus.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan sistem saat memperbarui status laporan.');
            } finally {
                this.loading = false;
            }
        },

        async sendReply(id = null) {
            const reportId = id || this.reportId;
            if (!this.counselorReply.trim()) {
                this.showNotice('Perhatian', 'Harap tulis balasan sebelum mengirim.');
                return;
            }
            this.loading = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/bk/reports/${reportId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        counselor_reply: this.counselorReply,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.showNotice('Terkirim', data.message || 'Balasan resmi konselor berhasil dikirimkan.');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal mengirim balasan.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan sistem saat mengirim balasan.');
            } finally {
                this.loading = false;
            }
        },

        async saveInternalNote(id = null) {
            const reportId = id || this.reportId;
            if (!this.internalNote.trim()) {
                this.showNotice('Perhatian', 'Harap tulis catatan sebelum menyimpan.');
                return;
            }
            this.loading = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/bk/reports/${reportId}/note`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        note: this.internalNote,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.showNotice('Tersimpan', data.message || 'Catatan rahasia berhasil disimpan.');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal menyimpan catatan.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan sistem saat menyimpan catatan.');
            } finally {
                this.loading = false;
            }
        },

        async updateCategory(id = null) {
            const reportId = id || this.reportId;
            if (!this.categoryChoice) {
                return;
            }
            this.loading = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/bk/reports/${reportId}/category`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        category: this.categoryChoice,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.showNotice('Tersimpan', data.message || 'Kategori kasus berhasil dikoreksi.');
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal mengoreksi kategori kasus.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan saat mengoreksi kategori.');
            } finally {
                this.loading = false;
            }
        },

        async submitForward(id = null) {
            const reportId = id || this.reportId;
            if (!this.forwardToUserId) {
                this.showNotice('Perhatian', 'Pilih Wali Kelas penerima terlebih dahulu.');
                return;
            }
            if (!this.summary.trim()) {
                this.showNotice('Perhatian', 'Tuliskan ringkasan terlebih dahulu.');
                return;
            }
            this.loading = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/bk/reports/${reportId}/forward`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        summary: this.summary,
                        to_user_id: Number(this.forwardToUserId),
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.closeModal();
                    this.showNotice('Berhasil', data.message || 'Ringkasan aman berhasil dikirim ke Wali Kelas.');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal meneruskan ringkasan.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan sistem saat meneruskan ke Wali Kelas.');
            } finally {
                this.loading = false;
            }
        },

        async confirmMarkIseng(id = null) {
            const reportId = id || this.reportId;
            this.loading = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/bk/reports/${reportId}/iseng`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.closeModal();
                    this.showNotice('Berhasil', data.message || 'Laporan telah ditandai iseng.');
                    setTimeout(() => window.location.href = '/bk', 1200);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal menandai laporan.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan saat memproses laporan.');
            } finally {
                this.loading = false;
            }
        },

        async confirmArchive(id = null) {
            const reportId = id || this.reportId;
            this.loading = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/bk/reports/${reportId}/archive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        archive_reason: this.archiveReason || 'selesai',
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.closeModal();
                    this.showNotice('Berhasil', data.message || 'Laporan berhasil diarsipkan.');
                    setTimeout(() => window.location.href = '/bk', 1200);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal mengarsipkan laporan.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan saat mengarsipkan laporan.');
            } finally {
                this.loading = false;
            }
        },

        async unarchiveCase(id = null) {
            const reportId = id || this.reportId;
            if (!confirm('Keluarkan laporan ini dari arsip dan kembalikan ke antrean aktif?')) return;
            this.loading = true;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/bk/reports/${reportId}/unarchive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.showNotice('Berhasil', data.message || 'Laporan berhasil dikeluarkan dari arsip.');
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    this.showNotice('Gagal', data.message || 'Gagal mengeluarkan laporan dari arsip.');
                }
            } catch (e) {
                console.error(e);
                this.showNotice('Error', 'Terjadi kesalahan saat memulihkan laporan.');
            } finally {
                this.loading = false;
            }
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
            const cutoff = new Date(Date.now() - 3 * 86400000).toISOString().slice(0, 10);
            if (!this.archive && this.priorityFilter === 'danger' && row.danger !== 'true') return false;
            if (!this.archive && this.priorityFilter === 'overdue' && row.date >= cutoff) return false;
            if (!this.archive && (this.status === 'handling' ? !['dibaca', 'ditindaklanjuti'].includes(row.status) : row.status !== this.status)) return false;
            const query = normalize(this.query).replace(/^#/, '').replace(/sr-\d{4}-/g, 'sr-');
            if (!row.text.includes(query)) return false;
            if (!Object.entries(this.filters).every(([key, value]) => !value || row[key] === value)) return false;
            if (this.period === 'custom') return (!this.dateFrom || row.date >= this.dateFrom) && (!this.dateTo || row.date <= this.dateTo);
            return !this.period || (row.date >= this.period);
        },
        get visibleRows() { return this.rows.filter((row, index) => this.rowVisible(index)); },
        get visibleCount() { return this.visibleRows.length; },
        get allSelected() { return this.visibleCount > 0 && this.visibleRows.every(row => this.selected.includes(row.id)); },
        selectVisible(checked) { this.selected = checked ? this.visibleRows.map(row => row.id) : []; },
        resetFilters() {
            this.query = '';
            this.priorityFilter = '';
            this.filters = { type: '', role: '', urgency: '', contact: '', reason: '' };
            this.period = '';
            this.dateFrom = '';
            this.dateTo = '';
            this.selected = [];
        },
    }));

    Alpine.data('walasDashboard', (config = {}) => ({
        filter: 'unread',
        query: '',
        rows: [],
        qrModalOpen: false,
        qrSvgHtml: '',
        reportUrl: config.reportUrl || (window.location.origin + '/s/lapor'),
        qrScanUrl: config.qrScanUrl || (window.location.origin + '/s/lapor'),
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
