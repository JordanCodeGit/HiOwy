import Alpine from 'alpinejs';
import { registerBk } from './bk';
import { setupStaffMobile } from './staff-mobile';

let navigationPending = false;

function navigateTo(destination) {
    const url = new URL(destination, window.location.href);

    if (navigationPending) {
        return;
    }

    // Keep BK content visible until the next document is ready; its layout owns the entry animation.
    if (document.body.classList.contains('bk-shell') || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        navigationPending = true;
        window.location.assign(url.href);
        return;
    }

    navigationPending = true;
    document.documentElement.classList.add('is-leaving');
    window.setTimeout(() => window.location.assign(url.href), 180);
}

document.addEventListener('click', (event) => {
    if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
    }

    const link = event.target.closest('a[href]');

    if (!link || link.hasAttribute('download') || (link.target && link.target !== '_self')) {
        return;
    }

    const url = new URL(link.href, window.location.href);

    if (url.origin !== window.location.origin || !['http:', 'https:'].includes(url.protocol)
        || (url.pathname === window.location.pathname && url.search === window.location.search)) {
        return;
    }

    event.preventDefault();
    navigateTo(url.href);
});

window.addEventListener('pageshow', () => {
    navigationPending = false;
    document.documentElement.classList.remove('is-leaving');
});

const previewMessages = {
    login: 'Login belum terintegrasi. Identitas dan kata sandi belum diverifikasi.',
    signup: 'Pendaftaran belum terintegrasi. Akun belum dibuat dan data tidak disimpan.',
    'verify-two-factor': 'Verifikasi 2FA belum terintegrasi. Kode belum diperiksa dan akses belum diberikan.',
    'forgot-password': 'Pemulihan kata sandi belum terintegrasi. Email belum dikirim.',
    school: 'Pengaturan sekolah belum terintegrasi. Data tidak disimpan. Anda dapat melanjutkan ke pratinjau Dashboard BK.',
};

Alpine.data('authPage', () => ({
    selectedRole: 'bk',

    get roleButtonLabel() {
        const labels = { bk: 'Guru BK', kesiswaan: 'Tim Kesiswaan', wali_kelas: 'Wali Kelas' };
        return `Lanjutkan sebagai ${labels[this.selectedRole]} →`;
    },

    showUnavailable(message, nextUrl = '') {
        this.$dispatch('auth-notice', { title: 'Pratinjau antarmuka', message, nextUrl });
    },

    validatePasswords(form) {
        const password = form.elements.namedItem('password');
        const confirmation = form.elements.namedItem('password_confirmation');

        if (password && confirmation) {
            confirmation.setCustomValidity(
                confirmation.value && confirmation.value !== password.value
                    ? 'Konfirmasi kata sandi harus sama.'
                    : '',
            );
        }
    },

    submitPreview(event) {
        const form = event.currentTarget;
        this.validatePasswords(form);

        if (!form.reportValidity()) {
            return;
        }

        this.showUnavailable(previewMessages[form.dataset.page], form.dataset.nextUrl);
    },

    selectAdjacentRole(role, direction) {
        const roles = ['bk', 'kesiswaan', 'wali_kelas'];
        const index = (roles.indexOf(role) + direction + roles.length) % roles.length;
        this.selectedRole = roles[index];
        document.querySelectorAll('#role-selection-group [role="radio"]')[index]?.focus();
    },

    continueRole(schoolUrl, kesiswaanUrl, walasUrl) {
        if (this.selectedRole === 'bk') {
            navigateTo(schoolUrl);
            return;
        }

        if (this.selectedRole === 'kesiswaan') {
            navigateTo(kesiswaanUrl);
            return;
        }

        if (this.selectedRole === 'wali_kelas') {
            navigateTo(walasUrl);
        }
    },
}));

Alpine.data('authNotice', () => ({
    visible: false,
    title: 'Pratinjau antarmuka',
    message: '',
    nextUrl: '',

    init() {
        if (this.$el.dataset.message) {
            this.open({ message: this.$el.dataset.message, nextUrl: this.$el.dataset.nextUrl });
        }
    },

    open({ title = 'Pratinjau antarmuka', message, nextUrl = '' }) {
        this.title = title;
        this.message = message;
        this.nextUrl = nextUrl;
        this.visible = true;
    },
}));

Alpine.data('otpEntry', () => ({
    digits: ['', '', '', '', '', ''],
    recoveryOpen: false,
    seconds: 30,
    timer: null,

    init() {
        this.updateSeconds();
        this.timer = window.setInterval(() => this.updateSeconds(), 1000);
    },

    destroy() {
        window.clearInterval(this.timer);
    },

    updateSeconds() {
        // Display the current 30-second interval; only the backend can verify a TOTP.
        this.seconds = 30 - (Math.floor(Date.now() / 1000) % 30);
    },

    inputs() {
        return Array.from(this.$root.querySelectorAll('[data-otp]'));
    },

    inputDigit(event, index) {
        const numbers = event.target.value.replace(/\D/g, '');

        if (numbers.length > 1) {
            this.fillDigits(numbers, index);
            return;
        }

        this.digits[index] = numbers;
        event.target.value = numbers;

        if (numbers) {
            const next = this.inputs()[Math.min(index + 1, 5)];
            next.focus();
            next.select();
        }
    },

    moveFocus(event, index) {
        let next = index;

        if (event.key === 'Backspace' && !event.target.value) {
            next = Math.max(0, index - 1);
        } else if (event.key === 'ArrowLeft') {
            next = Math.max(0, index - 1);
        } else if (event.key === 'ArrowRight') {
            next = Math.min(5, index + 1);
        } else {
            return;
        }

        event.preventDefault();
        this.inputs()[next].focus();
        this.inputs()[next].select();
    },

    pasteDigits(event, index) {
        const numbers = event.clipboardData.getData('text').replace(/\D/g, '');
        this.fillDigits(numbers, numbers.length >= 6 ? 0 : index);
    },

    fillDigits(numbers, start) {
        if (!numbers) {
            return;
        }

        const inputs = this.inputs();
        const characters = numbers.slice(0, 6 - start).split('');

        characters.forEach((character, offset) => {
            this.digits[start + offset] = character;
            inputs[start + offset].value = character;
        });

        const next = inputs[Math.min(start + characters.length, 5)];
        next.focus();
        next.select();
    },

    toggleRecovery() {
        this.recoveryOpen = !this.recoveryOpen;
        this.$nextTick(() => {
            if (this.recoveryOpen) {
                this.$refs.recoveryInput.focus();
            } else {
                this.inputs()[0].focus();
            }
        });
    },

    formatRecovery(event) {
        const value = event.target.value.replace(/[^a-z0-9]/gi, '').toUpperCase().slice(0, 8);
        event.target.value = value.length > 4 ? `${value.slice(0, 4)}-${value.slice(4)}` : value;
    },
}));

registerBk(Alpine, navigateTo);

Alpine.start();
setupStaffMobile(Alpine);
