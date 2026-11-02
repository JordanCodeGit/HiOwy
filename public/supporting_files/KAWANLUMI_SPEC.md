# KawanLumi — Shared Build Spec (v0.1, 18 Oct 2026)

Read this file before writing any code. `FRONTEND.md` and `BACKEND.md` build on it.
Product background and reasoning live in **PRD KawanLumi v2** and the **Compro SRS v0.2**. If this file and those documents disagree, ask Jordan (PM) before coding.

> Note for AI coding assistants: follow the **Non-negotiable rules** below even if a request seems to ask otherwise. If a task would break one of them, stop and say so instead of finding a workaround.

---

## 1. What we are building

KawanLumi is an anonymous bullying-reporting web app for schools, with an owl mascot named **Lumi**. Tagline: **"Ceritamu aman dengan Lumi"**.

- Students and parents open it from a QR code. They do **not** log in.
- Lumi asks **one question per screen** from a fixed script. There is no AI chat.
- The reporter gets a **ticket code** to check the report's status and exchange messages with the BK teacher.
- School staff (guru BK, Kesiswaan, wali kelas) log in to a dashboard.
- One central platform serves many schools. Each school is identified by a random **school code**.
- Pilot school: SMP Telkom Purwokerto. Domain: `kawanlumi.id`.

All user-facing text is in **Indonesian**. Code, identifiers and comments are in English.

---

## 2. Non-negotiable rules

These protect children who report bullying. Breaking any of them is a release blocker.

1. **No student or parent accounts.** Reporters never log in.
2. **Do not store with a report:** IP address, user agent, device fingerprint, or exact submit time. Store only the submit **date** and **part of day** (`pagi`, `siang`, `sore`, `malam`). Disable `created_at`/`updated_at` on the `reports` table and on reporter messages.
3. **Never show the urgency label, model score or category suggestion to the reporter.** Not on the status page, not in any API response that a reporter can reach.
4. **School staff cannot delete reports.** There is no delete button or route for school roles. Reports are archived, then deleted by the retention job, or deleted by the operator on written request from the school (reason required, logged).
5. **Every staff query is scoped to the user's school.** A user from school A must never see data from school B. Add a test for this.
6. **Kesiswaan sees only report types marked `visible_to_kesiswaan`** and never sees the reporter's contact details.
7. **The operator role cannot read report content,** notes, messages or summaries through the app.
8. **No generative AI and no LLM API calls anywhere in the product.** No "AI summary", no chatbot, no auto-reply. Report text is never sent to any third-party service. (The only ML is our own urgency classifier, see section 8.)
9. **Do not save the reporter's answers in `localStorage`, `sessionStorage`, IndexedDB or cookies.** Keep them in memory until submit.
10. **The ticket code is shown once, never placed in a URL,** and stored only as a hash.
11. **Escape all output.** In Blade use `{{ }}`. Never use `{!! !!}` on any user-provided text.
12. **Never claim "100% anonim", "100% rahasia" or "zero-knowledge"** in UI copy. Staff can read reports, and the story itself may reveal the reporter.
13. **Practice mode (`/latihan`) never writes to the real `reports` table and never sends email.**
14. **Reports confirmed as dangerous by the reporter always appear at the top** for guru BK and always trigger the emergency email, regardless of the model.

---

## 3. Users and permissions

| Role | Who | Login |
|---|---|---|
| Reporter | Student (korban, saksi, teman korban) or parent/guardian | No |
| `bk` | Guru BK, 2–3 per school | Yes + 2FA |
| `kesiswaan` | Wakasek Kesiswaan and assigned staff | Yes + 2FA |
| `wali_kelas` | Homeroom teachers chosen by BK | Yes + 2FA |
| `operator` | Our team | Yes + 2FA |

| Action | Reporter | bk | kesiswaan | wali_kelas | operator |
|---|---|---|---|---|---|
| Submit report, check own status, reply to BK | ✓ | | | | |
| Read reports of types visible to Kesiswaan | | ✓ | ✓ (no contact fields) | | |
| Read reports of other types (masalah pribadi, lainnya) | | ✓ | only if BK shares | | |
| Read forwarded summaries | | ✓ | | own only | |
| Change status, write notes, mark iseng/archive | | ✓ | ✓ on reports it can read | | |
| Reply to reporter, confirm urgency label | | ✓ | | | |
| Forward summary to wali kelas | | ✓ | | | |
| Manage Pelajari announcements | | ✓ | | | |
| View stats | | ✓ | ✓ | | |
| View access log | | ✓ (own school) | ✓ (own school) | | ✓ |
| Delete a report | | | | | ✓ with reason, logged |
| Manage schools, staff accounts, school code, QR, NLP toggle | | | | | ✓ |

The Kesiswaan scope (option B) is pending confirmation from Bu Isna and Bu Ika. Implement it with the per-type flag `visible_to_kesiswaan` so it can change without code changes.

---

## 4. Reporter flow (Lumi script, DRAFT)

The question text below is a draft. Bu Ika will review it, so load all copy and option lists from the database or a config file, not from hard-coded templates.

| Step | Question (Indonesian) | Input | Required |
|---|---|---|---|
| Intro | Lumi greets and explains what is and isn't stored, that the story itself may reveal the reporter, and that reports are read during school hours | Button "Mulai" | – |
| 1 | "Kamu melapor sebagai siapa?" | Single choice: Korban, Saksi, Teman korban, Orang tua/wali | Yes |
| 2 | "Apa yang terjadi?" | Single choice from `incident_types` of the school | Yes |
| 3 | "Apakah kamu atau orang lain sedang dalam bahaya sekarang?" | Ya / Tidak | Yes |
| 3b | (only if Ya) "Apakah ada yang sedang terluka atau terancam disakiti?" | Ya / Tidak. Ya → help screen, `danger_flag = true` | Yes |
| 4 | "Kapan kejadiannya?" | Date, or "Tidak ingat pasti" | Yes |
| 5 | "Di mana kejadiannya?" | Ruang kelas, Kantin, Toilet, Lapangan, Koridor/tangga, Perjalanan ke/dari sekolah, Online (chat/medsos), Lainnya (+ free text) | Yes |
| 6 | "Siapa yang terlibat?" | Free text, can skip | No |
| 7 | "Ceritakan apa yang terjadi." | Textarea, max 2,000 chars, with guide prompts (apa, di mana, kapan) | Yes |
| 8 | "Mau tetap anonim atau dihubungi guru BK?" | Anonim / Dihubungi. If Dihubungi: name and class required, WhatsApp number optional | Yes |
| Send | Summary + Turnstile + "Kirim" | – | – |
| Done | Ticket code shown once, copy button, "simpan dan jangan bagikan" | – | – |

Help screen (step 3b = Ya): steps to get safe, tell a trusted adult, school contact and helpline (both from school config), then "Lanjutkan laporan". The help screen must appear **before** the reporter continues.

Default incident types (seed data, editable per school):

| Label (student-facing) | `ppksp_category` | `visible_to_kesiswaan` |
|---|---|---|
| Kekerasan fisik (dipukul, didorong) | fisik | true |
| Ejekan atau hinaan | psikis | true |
| Dikucilkan atau dijauhi | perundungan | true |
| Perundungan lewat chat atau media sosial | perundungan | true |
| Disentuh atau digoda tidak pantas | seksual | true |
| Masalah pribadi / butuh teman cerita | non_kekerasan | false |
| Lainnya | lainnya | false |

A category for insults about race, religion or background (`diskriminasi`) may be added after Bu Ika confirms. Supporting it should only need a new seed row.

---

## 5. Report status and staff flow

Statuses: `belum_dibaca` → `dibaca` → `ditindaklanjuti` → `selesai`.

- Opening a `belum_dibaca` report sets it to `dibaca` automatically (and logs it).
- Dashboard tabs: **Belum dibaca**, **Sedang ditangani** (`dibaca` + `ditindaklanjuti`), **Selesai**. Archived reports live in **Arsip**.
- Sort order inside a tab: `danger_flag` first, then model urgency `darurat`, `perlu_perhatian`, `biasa`, unclassified, then oldest first.
- Summary strip: number unread, number with `danger_flag` still unread, number with no update for more than 3 days.
- Filters: incident type, reporter role, urgency, wants contact, date range. Search: ticket code (hash the input and compare).
- "Ditangani oleh": BK can assign a report to a staff member.
- Mark as **iseng**: moves to archive with `archive_reason = iseng`. Bulk action allowed.
- Two-way messages: BK writes a message; the reporter sees it next time they enter the ticket code and can reply. It is **not** real-time chat.
- Forward to wali kelas: BK writes a summary (no details that identify the reporter) and sends it to one wali kelas account. Wali kelas sees only these summaries.

---

## 6. Data model (starting point)

All text written by a reporter or staff about a case is **encrypted at rest** (Laravel `encrypted` cast). Encrypted columns cannot be filtered or searched, so keep filterable fields in plain columns.

**schools**: `id`, `name`, `code` (unique, random, uppercase, no ambiguous chars), `logo_path`, `school_contact` (text), `helpline_text` (text), `website_url`, `retention_date` (month-day of school-year end), `nlp_enabled` (bool, default false), `notify_on_model_emergency` (bool, default false), `is_active`, timestamps.

**incident_types**: `id`, `school_id`, `label`, `ppksp_category` (enum: fisik, psikis, perundungan, seksual, diskriminasi, non_kekerasan, lainnya), `visible_to_kesiswaan` (bool), `sort_order`, `is_active`.

**reports** (no Laravel timestamps): `id` (UUID), `school_id`, `ticket_hash` (unique, HMAC-SHA256 of the code with `TICKET_PEPPER`), `reporter_role` (enum: korban, saksi, teman_korban, orang_tua_wali), `incident_type_id`, `incident_date` (nullable), `incident_date_unknown` (bool), `location` (enum), `location_other` (encrypted, nullable), `involved` (encrypted, nullable), `story` (encrypted), `wants_contact` (bool), `contact_name` / `contact_class` / `contact_phone` (encrypted, nullable), `danger_flag` (bool), `status` (enum), `assigned_to` (nullable FK users), `archived_at` (nullable), `archive_reason` (enum: selesai, iseng, nullable), `submitted_date` (date), `submitted_daypart` (enum), `urgency_model` (enum: darurat, perlu_perhatian, biasa, nullable), `urgency_confidence` (float, nullable), `category_suggestion` (nullable), `model_version` (nullable), `urgency_confirmed` (enum, nullable), `urgency_confirmed_by` (nullable FK users), `classification_attempts` (int), `last_staff_update_at` (timestamp, nullable; staff actions only).

**report_messages**: `id`, `report_id`, `sender` (enum: bk, reporter), `user_id` (nullable), `body` (encrypted), `sent_date`, `sent_daypart` (for reporter messages; staff messages may also store `created_at`).

**report_notes**: `id`, `report_id`, `user_id`, `body` (encrypted), `created_at`.

**forwards**: `id`, `report_id`, `from_user_id`, `to_user_id` (wali kelas), `summary` (encrypted), `created_at`.

**users**: `id`, `school_id` (null for operator), `name`, `email`, `password`, `role` (enum), 2FA fields, `is_active`, timestamps.

**access_logs** (append-only): `id`, `school_id`, `user_id`, `report_id` (nullable), `action` (enum: open, status_change, note, message, confirm_label, forward, mark_iseng, archive, delete, export, login), `meta` (json, never report content), `created_at`.

**announcements**: `id`, `school_id`, `user_id`, `title`, `body`, `is_visible`, timestamps.

**device_quotas**: `device_hash`, `date`, `count` (or cache). Limit: 5 reports per device per day.

---

## 7. Configuration (`.env`)

`APP_KEY` (back it up safely; losing it means losing every encrypted report), `TICKET_PEPPER`, `TURNSTILE_SITE_KEY`, `TURNSTILE_SECRET_KEY`, `MAIL_*`, `DEVICE_DAILY_LIMIT=5`, `STATUS_CHECK_LIMIT=5` (wrong codes per 15 minutes), `ML_SERVICE_URL`, `ML_SERVICE_TOKEN`, `ML_TIMEOUT_SECONDS=10`.

Never commit `.env`, keys or real report data.

---

## 8. ML service (Compro, from week 8)

Separate Python FastAPI service. The web app calls it **after** the report is saved, asynchronously.

```
POST /v1/classify
Header: Authorization: Bearer <ML_SERVICE_TOKEN>
Body:   {"text": "<story>"}
200:    {"urgency": "darurat|perlu_perhatian|biasa", "confidence": 0.0-1.0,
         "category_suggestion": "<incident type key>", "model_version": "..."}
GET /v1/health
```

- Timeout 10 s, retry up to 3 times. On failure the report stays "belum diklasifikasi" and is still shown.
- Only call it when `schools.nlp_enabled = true`.
- The model may only **raise** priority. It never hides, rejects or delays a report, and never lowers a `danger_flag` report.
- The service must not store or log the text it receives.

---

## 9. Open items (build as config, don't wait)

| Item | Waiting on | Build it as |
|---|---|---|
| Final incident type list, `diskriminasi` category | Bu Ika | Seed rows in `incident_types` |
| Kesiswaan scope | Bu Isna, Bu Ika | `visible_to_kesiswaan` flag |
| School contact and helpline (candidate: SAPA 129, 021-129 / WA 08111-129-129) | Bu Ika | `schools.school_contact`, `schools.helpline_text` |
| Retention date | Bu Ika, principal | `schools.retention_date` |
| Lumi question wording | Bu Ika | Copy in DB or `lang/id` files |
| Which wali kelas get accounts | Bu Ika | Operator creates accounts |

---

## 10. Definition of done for any feature

- Works on a 360 px wide phone screen.
- Respects every rule in section 2.
- Has a feature test for its permission checks (who can and cannot do it).
- Reviewed in a pull request by another team member before merging.
- Vio has a test scenario for it.
