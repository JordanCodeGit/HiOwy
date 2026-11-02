# KawanLumi — Frontend Guide (v0.1, 18 Oct 2026)

Read `KAWANLUMI_SPEC.md` first. Section 2 (non-negotiable rules) applies to everything here.

Owners (IuM split): **Axan** builds the reporter pages, Lumi and Pelajari. **Kevin** builds the staff dashboard, the Kesiswaan and wali kelas views, and practice mode.

---

## 1. Suggested stack (confirm at sprint planning)

- **Laravel Blade + Tailwind CSS + Alpine.js.** Google Stitch exports HTML with Tailwind, so its screens can be turned into Blade views with little rework. Alpine handles the one-question-per-screen flow without a separate SPA.
- **Lumi animation:** CSS transitions between Axan's PNG poses first. Lottie (`lottie-web`) only if a pose truly needs it. Keep the total page weight small, since many students use cheap phones on mobile data.
- If the team prefers Inertia + React or Vue, decide on day 1 and update this file. Don't mix approaches.

---

## 2. Fix these in the Stitch designs before converting them

The Stitch screens were generated before Bu Isna's decisions. Remove or change:

- Name "SafeReport", "Lincoln High School", US grades (9th/10th) → **KawanLumi**, SMP Telkom Purwokerto, kelas 7–9.
- "100% rahasia", "Kerahasiaan & Hakmu Terjamin Sepenuhnya", "Enkripsi Zero-Knowledge", "Kerahasiaan 100%" → honest copy (see spec rule 12).
- **"Tingkat Urgensi: Prioritas" on the student's status page → remove.** The reporter never sees urgency (spec rule 3).
- "Unggah Bukti Baru (Foto/Tangkapan Layar)" → remove. Photo upload is out of scope.
- FAQ promising a review "dalam 1–2 jam" → remove. No response time has been agreed.
- Mading / public forum screens, admin sign-up pages, per-location QR "Physical Stations" with scan counts, "Show Reporter Name" toggles, reporter-name column, exact timestamps (e.g. "10:12 AM") → remove.
- Ticket codes with a fixed suffix like "LHS-9482-SAFE" → fully random codes such as `K7QM-4XTR-9W`.

---

## 3. Reporter pages (mobile first)

Routes (school code in the path, never the ticket code):

| Route | Page |
|---|---|
| `/` | Enter school code (for people typing the address from the poster) |
| `/s/{schoolCode}` | Lumi intro: what is stored and what isn't, "laporan dibaca pada jam sekolah", Mulai, link to Pelajari |
| `/s/{schoolCode}/lapor` | The question flow (spec section 4) |
| `/s/{schoolCode}/pelajari` | Learning page + school announcements |
| `/status` | Enter ticket code → status + messages + reply box |
| `/latihan/...` | Same pages with a visible "MODE LATIHAN" banner on every screen |

Requirements:

- **One question per screen**, Lumi at the top, progress "Pertanyaan X dari 8", Kembali and Lanjut buttons. Going back keeps earlier answers.
- **Lumi poses by step, never by what the reporter wrote:** waving (intro), thinking (questions), calm/caring (danger and help screen; ask Axan to create this pose), magnifier (status page), happy (report sent).
- **Story step:** shrink Lumi so the phone keyboard doesn't cover the textarea. Show a character counter (max 2,000).
- **Help screen** after a confirmed "Ya" on the danger question: steps, school contact and helpline from config, then "Lanjutkan laporan".
- **Ticket screen:** code in large monospace, copy button, reminder to save it and not share it. Shown once. After leaving, it can't be shown again.
- **Status page:** shows status (Belum dibaca / Dibaca / Ditindaklanjuti / Selesai) and messages from BK, plus a reply box. Never shows urgency or category suggestion. Submit the code in a POST body, never in the URL.
- **Quick exit button ("Keluar cepat")** on every reporter page: `window.location.replace('https://www.google.com')` (or another neutral site), so the back button doesn't return to KawanLumi.
- **No storage of answers** in localStorage, sessionStorage, IndexedDB or cookies (spec rule 9). Keep the flow state in Alpine/JS memory only.
- **Turnstile** widget on the final send step, plus a hidden honeypot field.
- **Accessibility:** WCAG 2.1 AA contrast, touch targets at least 44×44 px, works with keyboard, and turn off Lumi animation when `prefers-reduced-motion` is set.
- **Performance:** first screen under 3 s on 4G; moving between questions under 0.5 s.
- Optional: PWA manifest so the reporter pages can be added to the home screen.

---

## 4. Pelajari page

Sections: "Kalau kamu korban", "Kalau kamu melihat" (saksi), "Kalau temanmu jadi korban", "Untuk orang tua". Content is written by Jordan and Vio and reviewed by Bu Ika, so render it from Markdown/DB, not hard-coded HTML. Below it, list visible school announcements.

---

## 5. Staff dashboard (laptop first, usable on phone)

- **Login + 2FA** (TOTP code, recovery codes).
- **Sidebar:** Laporan, Arsip, Statistik, Halaman Pelajari (BK only), Log akses. Items depend on role.
- **Laporan:** summary strip, tabs Belum dibaca / Sedang ditangani / Selesai, filters, ticket search, table with urgency badge, incident type, reporter role, snippet, date + part of day, wants contact, assigned to.
- **Urgency badges must distinguish:** "DARURAT dari pelapor" (danger_flag), "Darurat (saran model)", model labels waiting for confirmation, labels confirmed by BK.
- **Report detail:** full report, timeline of status changes, notes, messages with reporter, contact details (BK only), actions: change status, assign, confirm/change urgency label, forward summary to wali kelas, mark iseng. **No delete button.**
- **Kesiswaan view:** same dashboard, but only reports whose type is visible to Kesiswaan, no contact fields, no reply box, no label confirmation.
- **Wali kelas view:** list of summaries forwarded to them. Nothing else.
- **Operator panel:** schools, staff accounts, school code + regenerate, QR download (PNG/PDF), NLP toggle, service health. No report content anywhere.
- Never display exact submit time of a report. Show date + part of day.

---

## 6. Copy rules

- Indonesian, friendly, suitable for 12–15 year olds. Lumi's lines will be reviewed by Bu Ika.
- Never promise 100% anonymity. Say plainly that the story itself can reveal who wrote it.
- Say that reports are read during school hours and that KawanLumi is not an emergency service.
