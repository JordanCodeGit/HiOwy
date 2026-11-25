import Alpine from 'alpinejs';
import { QRCode } from './qrcode';
import { registerBk } from './bk';
import { setupStaffMobile } from './staff-mobile';

window.QRCode = QRCode;

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

Alpine.data('staffInvitation', () => ({
    openInvitation() {
        const input = this.$refs.invitation;
        const url = new URL(input.value);
        const staffId = url.pathname.split('/').pop();
        const expectedPath = this.$el.dataset.invitationPath.replace('STAFF_ID', staffId);

        if (url.origin === location.origin && /^\d+$/.test(staffId) && url.pathname === expectedPath && url.searchParams.has('signature')) {
            location.assign(url.href);
            return;
        }

        input.setCustomValidity('Gunakan tautan undangan staf lengkap dari email sekolah ini.');
        input.reportValidity();
    },
}));

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
    verifyState: 'idle',

    inputs() {
        return Array.from(this.$root.querySelectorAll('[data-otp]'));
    },

    async checkOtpServer() {
        const code = this.digits.join('');
        if (code.length < 6) {
            this.verifyState = 'idle';
            return;
        }
        this.verifyState = 'checking';
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content
                || this.$root.querySelector('input[name="_token"]')?.value
                || '';
            const res = await fetch('/two-factor/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ code }),
            });
            const data = await res.json();
            if (this.digits.join('') === code) {
                this.verifyState = data.valid ? 'valid' : 'invalid';
            }
        } catch {
            this.verifyState = 'idle';
        }
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

        this.checkOtpServer();
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

        this.checkOtpServer();
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
