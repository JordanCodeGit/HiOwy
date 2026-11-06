import { lockOverlayScroll, unlockOverlayScroll } from './overlay-scroll';

const compact = window.matchMedia('(max-width: 1023px)');
const plain = element => {
    const copy = element.cloneNode(true);
    copy.querySelectorAll('.material-symbols-outlined, input, button').forEach(node => node.remove());
    return copy.textContent.replace(/\s+/g, ' ').trim();
};
const node = (tag, className, text) => {
    const element = document.createElement(tag);
    if (className) element.className = className;
    if (text) element.textContent = text;
    return element;
};
const button = (label, action, className = '') => {
    const element = node('button', className, label);
    element.type = 'button';
    element.addEventListener('click', action);
    return element;
};

function wireSheet(dialog) {
    let trigger;
    const open = () => {
        if (dialog.open) return;
        trigger = document.activeElement;
        dialog.showModal();
        lockOverlayScroll(dialog);
        dialog.querySelector('button, input, select')?.focus({ preventScroll: true });
    };
    dialog.addEventListener('close', () => {
        unlockOverlayScroll(dialog);
        const anotherOverlay = [...document.querySelectorAll('dialog[open], .bk-modal')].some(element => element !== dialog && element.getClientRects().length > 0);
        if (!anotherOverlay) {
            if (document.activeElement === document.body) trigger?.focus({ preventScroll: true });
        }
    });
    dialog.addEventListener('click', event => { if (event.target === dialog) dialog.close(); });
    dialog.querySelectorAll('[data-close-sheet]').forEach(close => close.addEventListener('click', () => dialog.close()));
    compact.addEventListener('change', () => { if (!compact.matches) dialog.close(); });
    return open;
}

function sheet(parent, title, full = false) {
    const dialog = node('dialog', `staff-sheet${full ? ' staff-full-sheet' : ''}`);
    const heading = node('div', 'staff-sheet-heading');
    const label = node('h2', '', title);
    label.id = `staff-sheet-${document.querySelectorAll('.staff-sheet').length}`;
    dialog.setAttribute('aria-labelledby', label.id);
    const close = button('×', () => dialog.close());
    close.setAttribute('aria-label', 'Tutup');
    heading.append(label, close);
    const content = node('div', 'staff-sheet-content');
    dialog.append(heading, content);
    parent.append(dialog);
    return { dialog, content, open: wireSheet(dialog) };
}

function responsiveMove(Alpine, elements, destination, media = compact) {
    const entries = elements.filter(Boolean).map(element => {
        const anchor = document.createComment('staff-responsive-position');
        element.before(anchor);
        return { element, anchor };
    });
    const update = () => Alpine.mutateDom(() => {
        entries.forEach(({ element, anchor }) => {
            if (media.matches) destination.append(element);
            else anchor.after(element);
        });
    });
    media.addEventListener('change', update);
    update();
}

function enhanceTables(main) {
    const bound = new WeakSet();
    const annotate = () => main.querySelectorAll('table').forEach(table => {
        table.classList.add('staff-card-table');
        table.setAttribute('role', 'table');
        const headings = [...(table.tHead?.rows[0]?.cells || [])].map(plain);
        [...table.tBodies].forEach(body => [...body.rows].forEach(row => {
            if (bound.has(row)) return;
            bound.add(row);
            row.setAttribute('role', 'row');
            [...row.cells].forEach((cell, index) => {
                const label = headings[index] || '';
                cell.dataset.label = label;
                cell.setAttribute('role', 'cell');
                let kind = 'field';
                if (!label && cell.querySelector('input[type="checkbox"]')) kind = 'selection';
                else if (/kode tiket|laporan terkait/i.test(label)) kind = 'ticket';
                else if (/urgensi/i.test(label)) kind = 'urgency';
                else if (/cuplikan/i.test(label)) kind = 'story';
                else if (/aksi/i.test(label) && !/sistem/i.test(label)) kind = 'actions';
                else if (/urutan/i.test(label)) kind = 'order';
                else if (/nama|jenis kejadian/i.test(label)) kind = 'title';
                cell.dataset.cell = kind;
            });
            if (row.dataset.danger === 'true' || row.querySelector('[data-cell="urgency"]')?.textContent.includes('Darurat')) row.classList.add('staff-danger-card');
            row.addEventListener('click', event => {
                if (!compact.matches || event.target.closest('a, button, input, select, label, textarea')) return;
                row.querySelector('[data-cell="actions"] a')?.click();
            });
        }));
    });
    annotate();
    new MutationObserver(annotate).observe(main, { childList: true, subtree: true });
}

function enhanceFilters(main, Alpine) {
    const state = main.getAttribute('x-data') || '';
    if (!/bkTable|kesiswaanTable|bkStatistics/.test(state) && document.body.dataset.staffPage !== 'access-log') return;
    const controls = [...main.querySelectorAll('select')].filter(select => !select.options[0]?.textContent.includes('/ halaman'));
    if (!controls.length) return;
    const filterSheet = sheet(main, 'Filter');
    filterSheet.dialog.classList.add('staff-filter-sheet');
    const groups = controls.map(select => {
        let group = select.closest('label') || select.parentElement;
        if (group.querySelectorAll('select').length > 1) group = select;
        group.classList.add('staff-filter-field');
        return group;
    });
    const dates = [...main.querySelectorAll('input[type="date"]')];
    main.querySelectorAll('button').forEach(element => {
        if ((element.getAttribute('@click') || '').includes('dateFilterOpen')) element.classList.add('staff-desktop-only');
    });
    dates.forEach(input => {
        const group = input.closest('label') || input;
        if (group.parentElement.hasAttribute('x-show')) group.parentElement.classList.add('staff-desktop-only');
        if (!groups.some(parent => parent.contains(group))) groups.push(group);
        input.addEventListener('change', () => {
            if (!compact.matches || !input.value) return;
            const period = controls.find(select => [...select.options].some(option => option.value === 'custom'));
            if (period && period.value !== 'custom') {
                period.value = 'custom';
                period.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
    // Keep the filter dialog before the table so existing select-index filters keep their order.
    const firstGroup = groups[0];
    firstGroup.before(filterSheet.dialog);
    const toolbar = node('div', 'staff-mobile-filter-toolbar staff-mobile-only');
    const show = button('Filter', filterSheet.open, 'staff-filter-button');
    const chips = node('div', 'staff-filter-chips staff-mobile-only');
    const search = main.querySelector('input[type="search"], input[x-model="query"]');
    const searchGroup = search?.closest('label') || search?.parentElement;
    if (searchGroup) searchGroup.after(toolbar, chips);
    else filterSheet.dialog.before(toolbar, chips);
    toolbar.append(show);
    const clearControl = control => {
        control.value = control.tagName === 'SELECT' ? control.options[0].value : '';
        control.dispatchEvent(new Event('input', { bubbles: true }));
        control.dispatchEvent(new Event('change', { bubbles: true }));
    };
    const refresh = () => {
        chips.replaceChildren();
        const active = [...controls, ...dates].filter(control => control.tagName === 'SELECT' ? control.selectedIndex > 0 : !!control.value);
        show.textContent = active.length ? `Filter · ${active.length}` : 'Filter';
        active.forEach(control => {
            const label = control.tagName === 'SELECT' ? control.selectedOptions[0].textContent.trim() : control.value;
            const chip = button(`${label} ×`, () => { clearControl(control); refresh(); });
            chip.setAttribute('aria-label', `Hapus filter ${label}`);
            chips.append(chip);
        });
    };
    const footer = node('div', 'staff-sheet-footer');
    footer.append(button('Reset', () => { [...controls, ...dates].forEach(clearControl); refresh(); }), button('Terapkan', () => filterSheet.dialog.close(), 'staff-primary'));
    filterSheet.dialog.append(footer);
    responsiveMove(Alpine, [...new Set(groups)], filterSheet.content);
    main.addEventListener('change', refresh);
    filterSheet.dialog.addEventListener('close', refresh);
    refresh();
    compact.addEventListener('change', () => { if (!compact.matches) filterSheet.dialog.close(); });
}

function enhanceSelection(main) {
    if (document.body.dataset.staffPage !== 'dashboard' || document.body.dataset.staffPortal === 'walas') return;
    const action = [...main.querySelectorAll('button')].find(element => element.id === 'bulkActionBtn' || (element.getAttribute('@click') || '').includes('bulk-iseng'));
    if (!action) return;
    action.classList.add('staff-desktop-only');
    const controls = main.querySelector('.staff-mobile-filter-toolbar') || main;
    const toggle = button('Pilih', () => {
        main.classList.toggle('staff-selecting');
        toggle.textContent = main.classList.contains('staff-selecting') ? 'Batal pilih' : 'Pilih';
        if (!main.classList.contains('staff-selecting')) clear();
    });
    toggle.classList.add('staff-mobile-only');
    controls.append(toggle);
    const bar = node('div', 'staff-selection-bar');
    const count = node('span');
    const clear = () => {
        main.querySelectorAll('tbody input[type="checkbox"]:checked').forEach(input => { input.checked = false; input.dispatchEvent(new Event('change', { bubbles: true })); });
        main.classList.remove('staff-selecting');
        toggle.textContent = 'Pilih';
        update();
    };
    bar.append(count, button('Tandai iseng', () => action.click(), 'staff-primary'), button('Batal', clear));
    main.append(bar);
    const update = () => requestAnimationFrame(() => {
        const checked = main.querySelectorAll('tbody input[type="checkbox"]:checked').length;
        count.textContent = `${checked} dipilih`;
        bar.classList.toggle('is-visible', checked > 0);
    });
    main.addEventListener('change', update);
    main.addEventListener('input', update);
    main.addEventListener('click', update);
    update();
}

function enhanceSummaryStrip(main, Alpine) {
    if (document.body.dataset.staffPage !== 'dashboard' || document.body.dataset.staffPortal === 'walas') return;
    const strip = main.querySelector(':scope > section.grid');
    if (!strip) return;
    const state = Alpine.$data(main);
    const clear = button('Semua prioritas ×', () => { state.priorityFilter = ''; clear.hidden = true; }, 'staff-priority-chip staff-mobile-only');
    clear.hidden = true; strip.after(clear);
    [...strip.children].forEach((card, index) => {
        card.setAttribute('role', 'button'); card.tabIndex = 0;
        const apply = () => {
            state.priorityFilter = ['', 'danger', 'overdue'][index] || '';
            if ('statusTab' in state) state.statusTab = 0;
            if ('status' in state) state.status = 'belum_dibaca';
            state.selected = [];
            clear.hidden = !state.priorityFilter;
            clear.textContent = `${index === 1 ? 'Danger flag' : 'Tanpa update >3 hari'} ×`;
        };
        card.addEventListener('click', apply);
        card.addEventListener('keydown', event => { if (['Enter', ' '].includes(event.key)) { event.preventDefault(); apply(); } });
    });
    main.querySelectorAll('button').forEach(tab => {
        if (/statusTab\s*=|status\s*=/.test(tab.getAttribute('@click') || '')) {
            tab.parentElement.classList.add('staff-list-tabs');
            tab.parentElement.parentElement.classList.add('staff-list-tabs-shell');
        }
    });
}

function enhanceDetail(main, Alpine) {
    if (document.body.dataset.staffPage !== 'report') return;
    const bk = document.body.dataset.staffPortal === 'bk';
    const groups = { Isi: [], ...(bk ? { Percakapan: [] } : {}), Catatan: [], Riwayat: [] };
    let actionPanel;
    main.querySelectorAll('section').forEach(section => {
        const title = section.querySelector('h2')?.textContent || '';
        if (/Status.*Pen/.test(title)) actionPanel = section;
        else if (/Informasi Insiden|Identitas Pelapor/.test(title)) groups.Isi.push(section);
        else if (/Komunikasi/.test(title)) groups.Percakapan?.push(section);
        else if (/Catatan|LAPORKAN/.test(title)) groups.Catatan.push(section);
        else if (/Riwayat/.test(title)) groups.Riwayat.push(section);
    });
    const panels = node('div', 'staff-detail-panels staff-mobile-only');
    const tabs = node('nav', 'staff-detail-tabs staff-mobile-only');
    tabs.setAttribute('aria-label', 'Bagian laporan');
    const firstGrid = main.querySelector('.grid.lg\\:grid-cols-12');
    if (!firstGrid) return;
    firstGrid.classList.add('staff-detail-original-grid');
    firstGrid.before(tabs, panels);
    const buttons = [];
    const sections = [];
    Object.entries(groups).forEach(([name, items], index) => {
        const panel = node('div', 'staff-detail-panel');
        panel.id = `staff-detail-${index}`;
        panel.hidden = index !== 0;
        const tab = button(name, () => {
            sections.forEach(element => { element.hidden = element !== panel; });
            buttons.forEach(element => element.setAttribute('aria-pressed', String(element === tab)));
        });
        tab.setAttribute('aria-controls', panel.id);
        tab.setAttribute('aria-pressed', String(index === 0));
        buttons.push(tab); sections.push(panel); tabs.append(tab); panels.append(panel);
        responsiveMove(Alpine, items, panel);
    });
    const actions = sheet(main, 'Aksi laporan');
    if (actionPanel) responsiveMove(Alpine, [actionPanel], actions.content);
    const dock = node('div', 'staff-action-dock staff-mobile-only');
    dock.append(button('Aksi laporan', actions.open, 'staff-primary'));
    main.append(dock);
    const actionButtons = [...main.querySelectorAll('button')].filter(element => !actions.dialog.contains(element) && /Arsipkan|Laporkan ke Wali Kelas|Tandai.*Iseng/i.test(element.textContent) && !element.closest('.bk-modal'));
    actionButtons.forEach(original => {
        original.classList.add('staff-desktop-only');
        actions.content.append(button(plain(original), () => { actions.dialog.close(); original.click(); }));
    });
    actions.content.append(button('Tulis catatan', () => {
        actions.dialog.close();
        buttons.find(element => element.textContent === 'Catatan')?.click();
        panels.querySelector('.staff-detail-panel:not([hidden]) textarea')?.focus();
    }));
    actions.content.addEventListener('click', event => {
        const target = event.target.closest('button');
        if (target && !target.closest('form') && /unavailable|openModal/.test(target.getAttribute('@click') || '')) actions.dialog.close();
    });
    actions.content.addEventListener('submit', () => actions.dialog.close());
    const identity = groups.Isi.find(section => section.textContent.includes('Identitas Pelapor'));
    if (identity) {
        const heading = identity.querySelector('h2');
        const content = [...identity.children].slice(1);
        const toggle = button('Lihat kontak pelapor', () => { identity.classList.toggle('staff-contact-open'); toggle.setAttribute('aria-expanded', String(identity.classList.contains('staff-contact-open'))); }, 'staff-mobile-only');
        toggle.setAttribute('aria-expanded', 'false'); heading?.parentElement?.after(toggle);
        content.forEach(element => element.classList.add('staff-contact-content'));
    }
    main.querySelector('#counselorReply')?.parentElement.classList.add('staff-reply-composer');
    const progress = main.querySelector('.grid.md\\:grid-cols-4');
    if (progress) {
        progress.classList.add('staff-desktop-only');
        progress.before(node('p', 'staff-mobile-only staff-progress-label', 'Langkah 2 dari 4: Dibaca'));
    }
}

function enhanceProfile(main, Alpine) {
    if (document.body.dataset.staffPage !== 'profile') return;
    let cards = [...main.querySelectorAll('.bk-hover-card')];
    if (!cards.length) cards = [...main.querySelectorAll('.lg\\:col-span-5 > .bg-surface-container-lowest, .lg\\:col-span-7 > .bg-surface-container-lowest')];
    const groups = [];
    if (document.body.dataset.staffPortal === 'walas' && cards.length === 3) {
        groups.push(['Data akun', [cards[0]]], ['Kata sandi', [cards[1]]]);
        const security = cards[2];
        security.classList.add('staff-profile-original');
        groups.push(['2FA', [security.children[0]]], ['Sesi', [security.children[1]]]);
    } else {
        groups.push(['Data akun', cards.slice(0, 2)], ['Kata sandi', [cards[2]]], ['2FA', [cards[3]]], ['Sesi', [cards[4]]]);
    }
    groups.forEach(([label, elements], index) => {
        if (!elements[0]) return;
        const details = node('details', 'staff-profile-section');
        const summary = node('summary', '', label);
        elements[0].before(details);
        details.append(summary);
        responsiveMove(Alpine, elements, details);
        const update = () => { details.open = compact.matches ? index === 0 : true; };
        compact.addEventListener('change', update); update();
    });
    const revealAnchor = () => {
        if (!location.hash) return;
        const target = document.getElementById(location.hash.slice(1));
        const section = target?.closest('.staff-profile-section');
        if (section) { section.open = true; requestAnimationFrame(() => target.scrollIntoView({ block: 'start' })); }
    };
    window.addEventListener('hashchange', revealAnchor);
    revealAnchor();
}

function enhanceLearn(main, Alpine) {
    if (document.body.dataset.staffPage !== 'learn') return;
    const form = main.querySelector('#announcement-form');
    if (!form) return;
    const editor = form.closest('.bk-hover-card');
    const editSheet = sheet(main, 'Pengumuman', true);
    responsiveMove(Alpine, [editor], editSheet.content);
    const dock = node('div', 'staff-action-dock staff-mobile-only');
    dock.append(button('Tambah pengumuman', () => { Alpine.$data(main).newAnnouncement(); editSheet.open(); }, 'staff-primary'));
    main.append(dock);
    main.querySelector('#btn-focus-form')?.classList.add('staff-desktop-only');
    const audience = main.querySelector('#audience-selector');
    audience?.parentElement.classList.add('staff-desktop-only');
    main.querySelectorAll('.bk-learning-card span').forEach(element => {
        if (/\d+ Tayangan/.test(element.textContent) && !element.querySelector('span:not(.material-symbols-outlined)')) element.classList.add('staff-desktop-only');
    });
    main.addEventListener('click', event => {
        const action = event.target.closest('button');
        if (compact.matches && action && /editAnnouncement/.test(action.getAttribute('@click') || '')) editSheet.open();
    });
    editSheet.content.addEventListener('click', event => {
        const action = event.target.closest('button');
        if (action && (action.getAttribute('@click') || '').includes('saveAnnouncement') && form.checkValidity()) editSheet.dialog.close();
    });
    form.querySelectorAll('input[type="text"], textarea').forEach(input => {
        if (!input.maxLength || input.maxLength < 0) input.maxLength = input.tagName === 'TEXTAREA' ? 3000 : 160;
        const counter = node('span', 'staff-field-counter');
        input.after(counter);
        const update = () => { counter.textContent = `${input.value.length}/${input.maxLength}`; };
        input.addEventListener('input', update); update();
    });
}

function preserveListState(main, Alpine) {
    if (!/bkTable|kesiswaanTable|walasDashboard/.test(main.getAttribute('x-data') || '')) return;
    const fields = ['query', 'status', 'statusTab', 'filters', 'dateFrom', 'dateTo', 'period', 'filter', 'priorityFilter'];
    const state = Alpine.$data(main);
    const saved = history.state?.staffList;
    if (saved?.path === location.pathname) {
        fields.forEach(key => { if (saved.values[key] !== undefined && key in state) state[key] = saved.values[key]; });
        [...main.querySelectorAll('select')].forEach((select, index) => { if (saved.selects[index] !== undefined) select.value = saved.selects[index]; });
        requestAnimationFrame(() => window.scrollTo(0, saved.scroll));
        main.dispatchEvent(new Event('change', { bubbles: true }));
    }
    document.addEventListener('click', event => {
        if (!event.target.closest('a[href]')) return;
        const values = {};
        fields.forEach(key => { if (key in state) values[key] = state[key]; });
        history.replaceState({ ...history.state, staffList: JSON.parse(JSON.stringify({ path: location.pathname, values, limit: Number(main.dataset.mobileLimit || 6), selects: [...main.querySelectorAll('select')].map(select => select.value), scroll: window.scrollY })) }, '');
    }, true);
}

function enhancePagination(main) {
    if (!['dashboard', 'archive', 'access-log'].includes(document.body.dataset.staffPage)) return;
    const rows = [...main.querySelectorAll('[data-preview-row], [data-report-row], [data-summary-card]')];
    if (!rows.length) return;
    let limit = history.state?.staffList?.limit || 6;
    const load = button('Muat lebih banyak', () => { limit += 6; update(); }, 'staff-load-more staff-mobile-only');
    const count = node('p', 'staff-mobile-count staff-mobile-only');
    main.append(count, load);
    main.querySelectorAll('[x-text]').forEach(element => {
        if ((element.getAttribute('x-text') || '').includes('visibleCount') && !element.closest('button')) {
            const paragraph = element.closest('p') || element.closest('.font-body-sm') || element;
            paragraph.classList.add('staff-desktop-only');
        }
    });
    main.querySelector('[aria-label="Filter Ringkasan"]')?.classList.add('staff-list-tabs');
    const update = () => {
        const visible = rows.filter(row => row.style.display !== 'none');
        rows.forEach(row => row.classList.remove('staff-extra-row'));
        if (compact.matches) visible.slice(limit).forEach(row => row.classList.add('staff-extra-row'));
        load.hidden = visible.length <= limit;
        count.textContent = `${Math.min(visible.length, limit)} dari ${visible.length} ditampilkan`;
        main.dataset.mobileLimit = String(limit);
    };
    let frame;
    const queue = () => { cancelAnimationFrame(frame); frame = requestAnimationFrame(update); };
    rows.forEach(row => new MutationObserver(queue).observe(row, { attributes: true, attributeFilter: ['style'] }));
    compact.addEventListener('change', update);
    main.querySelectorAll('select').forEach(select => {
        if (select.options[0]?.textContent.includes('/ halaman')) select.parentElement.classList.add('staff-desktop-only');
    });
    main.querySelectorAll('button').forEach(element => {
        if ((element.getAttribute('@click') || '').includes('Halaman data berikutnya')) element.classList.add('staff-desktop-only');
    });
    update();
}

function formAndNetworkStates(main) {
    const changed = new Set();
    const originals = new WeakMap();
    const fields = 'form input:not([type="hidden"]), form textarea, textarea, .bk-modal input:not([type="hidden"])';
    document.querySelectorAll(fields).forEach(input => originals.set(input, input.value));
    document.addEventListener('input', event => {
        const input = event.target;
        if (!originals.has(input)) return;
        if (input.value !== originals.get(input)) changed.add(input); else changed.delete(input);
        const error = document.getElementById(`${input.id}-mobile-error`);
        if (error && input.validity.valid) {
            input.setAttribute('aria-describedby', (input.getAttribute('aria-describedby') || '').split(' ').filter(id => id !== error.id).join(' '));
            error.remove(); input.removeAttribute('aria-invalid');
        }
    });
    const unload = event => { if (changed.size) { event.preventDefault(); event.returnValue = ''; } };
    window.addEventListener('beforeunload', unload);
    document.addEventListener('submit', () => {
        changed.clear();
        window.removeEventListener('beforeunload', unload);
    });
    document.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link || link.target === '_blank' || link.hasAttribute('download') || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) return;
        const target = new URL(link.href, location.href);
        if (target.pathname === location.pathname && target.hash) return;
        if (changed.size && !window.confirm('Ada perubahan yang belum disimpan. Tetap tinggalkan halaman?')) {
            event.preventDefault(); event.stopImmediatePropagation(); return;
        }
        window.removeEventListener('beforeunload', unload);
        if (target.origin === location.origin) document.body.classList.add('staff-navigating');
        if (['report', 'summary'].includes(document.body.dataset.staffPage) && document.referrer && new URL(document.referrer).href === target.href && history.length > 1) {
            event.preventDefault(); event.stopImmediatePropagation(); history.back();
        }
    }, true);
    let fieldNumber = 0;
    document.addEventListener('invalid', event => {
        const input = event.target;
        if (!input.id) {
            do { input.id = `staff-input-${++fieldNumber}`; }
            while (document.querySelectorAll(`#${input.id}`).length > 1);
        }
        let error = document.getElementById(`${input.id}-mobile-error`);
        if (!error) { error = node('p', 'staff-field-error'); error.id = `${input.id}-mobile-error`; error.setAttribute('aria-live', 'polite'); (input.closest('.relative') || input).after(error); }
        error.textContent = input.validationMessage;
        input.setAttribute('aria-invalid', 'true');
        input.setAttribute('aria-describedby', [...new Set([...(input.getAttribute('aria-describedby') || '').split(' ').filter(Boolean), error.id])].join(' '));
    }, true);
    const offline = node('div', 'staff-network-banner');
    offline.setAttribute('role', 'status'); offline.append(node('span', '', 'Koneksi terputus.'), button('Coba lagi', () => location.reload()));
    document.body.append(offline);
    const update = () => { offline.hidden = navigator.onLine; };
    window.addEventListener('online', update); window.addEventListener('offline', update); update();
    // The authenticated backend can emit this event after receiving a session-expired response.
    window.addEventListener('staff:session-expired', () => {
        const expired = sheet(document.body, 'Sesi berakhir');
        expired.content.append(node('p', '', 'Silakan masuk kembali untuk melanjutkan.'));
        const login = node('a', 'staff-primary', 'Masuk kembali');
        login.href = document.body.dataset.staffLogin;
        expired.content.append(login);
        expired.open();
    }, { once: true });
    window.addEventListener('pageshow', () => document.body.classList.remove('staff-navigating'));
}

export function setupStaffMobile(Alpine) {
    if (!document.body.dataset.staffPortal) return;
    const main = document.querySelector('#bk-content main');
    if (!main) return;
    const menu = document.getElementById('staff-more-menu');
    if (menu) {
        const open = wireSheet(menu);
        const trigger = document.querySelector('[data-staff-menu]');
        trigger.addEventListener('click', () => { trigger.setAttribute('aria-expanded', 'true'); open(); });
        menu.addEventListener('close', () => trigger.setAttribute('aria-expanded', 'false'));
    }
    enhanceTables(main);
    enhanceFilters(main, Alpine);
    enhanceSelection(main);
    enhanceSummaryStrip(main, Alpine);
    enhanceDetail(main, Alpine);
    enhanceProfile(main, Alpine);
    enhanceLearn(main, Alpine);
    enhancePagination(main);
    preserveListState(main, Alpine);
    formAndNetworkStates(main);
    const keyboard = () => {
        const editing = document.activeElement?.matches('input, textarea, select');
        document.body.classList.toggle('staff-keyboard-open', !!editing && (window.visualViewport?.height || innerHeight) < innerHeight * .8);
        document.body.classList.toggle('staff-reply-focused', document.activeElement?.id === 'counselorReply');
        document.documentElement.style.setProperty('--staff-viewport-height', `${window.visualViewport?.height || innerHeight}px`);
        document.documentElement.style.setProperty('--staff-keyboard-inset', `${Math.max(0, innerHeight - (window.visualViewport?.height || innerHeight) - (window.visualViewport?.offsetTop || 0))}px`);
    };
    window.visualViewport?.addEventListener('resize', keyboard);
    document.addEventListener('focusout', keyboard);
    document.addEventListener('focusin', keyboard);
    keyboard();
}
