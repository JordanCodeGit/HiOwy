# KawanLumi — Backend Guide (v0.1, 18 Oct 2026)

Read `KAWANLUMI_SPEC.md` first. Section 2 (non-negotiable rules) applies to everything here.

Owners (IuM split): **Aksa** builds the core backend (database, auth/2FA, tickets, quotas, logs, email, retention, encryption, ML integration). **Kevin** builds the dashboard backend and deployment, and backs up Aksa.

---

## 1. Stack and hosting

- **Laravel** (latest stable version supported by the hosting's PHP, PHP 8.2+), **MySQL 8 / MariaDB 10.6+**.
- **2FA:** Laravel Fortify's two-factor authentication (TOTP + recovery codes), required for every staff account.
- **Hosting:** one central app for all schools on `kawanlumi.id`. Shared hosting has no long-running worker, so:
  - `QUEUE_CONNECTION=database`
  - cron every minute: `php artisan schedule:run`, which runs `queue:work --stop-when-empty`
  - the **emergency email** is dispatched after the response (`dispatch(...)->afterResponse()`) so it goes out in under 1 minute without slowing the reporter.
- ML service (Compro) runs separately on a VPS. See spec section 8.

---

## 2. Security essentials

- **Encryption at rest:** use the `encrypted` cast for `story`, `involved`, `location_other`, `contact_*`, message bodies, notes and forward summaries. Back up `APP_KEY` outside the server and the repo.
- **Ticket codes:** 10 characters from `ABCDEFGHJKMNPQRSTUVWXYZ23456789` (no 0, O, 1, I, L) using a cryptographically secure random generator. Display as `XXXX-XXXX-XX`. Store only `hash_hmac('sha256', normalizedCode, TICKET_PEPPER)`. Normalize input (uppercase, strip dashes and spaces) before hashing.
- **Status check rate limit:** max 5 wrong codes per 15 minutes per IP and per device cookie (Laravel `RateLimiter`, cache only, never written to the report).
- **Report submit limit:** 5 reports per device per day. Device = random ID in an httpOnly cookie, stored hashed. No per-IP limit on submissions (the whole school shares one WiFi IP).
- **Turnstile:** verify the token server-side at `https://challenges.cloudflare.com/turnstile/v0/siteverify` before saving. Reject if the honeypot field is filled.
- **No IP, user agent or exact time on reports** (spec rule 2). Set `public $timestamps = false;` on `Report`. Check that no middleware, log channel or error tracker writes request bodies or IPs next to report data.
- **School scoping:** a global scope on every school-owned model that filters by the logged-in user's `school_id`. Operator routes use separate controllers that never select content columns.
- **Authorization:** Laravel Policies for every report action, following spec section 3. Kesiswaan policy checks `incident_type.visible_to_kesiswaan` and hides contact fields.
- **Sessions:** staff sessions expire after 30 minutes idle. Lock login for a while after 5 failed attempts.
- **Output:** never return encrypted fields in JSON to roles that can't read them. Never return urgency/model fields on any reporter-facing route.

---

## 3. Routes (web)

Reporter (no auth):

| Method | Route | Does |
|---|---|---|
| GET | `/s/{schoolCode}` | Resolve school by code (404 page if invalid or inactive) |
| POST | `/s/{schoolCode}/lapor` | Validate, Turnstile, honeypot, device quota → save report → classify async (if NLP on) → emergency email if `danger_flag` → redirect to ticket page (code passed once via flash session) |
| POST | `/status` | Throttled lookup by ticket hash → status + messages (no model fields) |
| POST | `/status/reply` | Requires the ticket code again in the body → save reporter message (date + part of day only) |
| POST | `/latihan/...` | Same validation, **nothing saved, no email** |

Staff (auth + 2FA, role middleware): dashboard list, report detail, status change, assign, notes, messages, confirm label, forward, mark iseng / bulk iseng, archive list, stats, announcements (BK), access log, wali kelas forwards. Operator: schools CRUD, staff accounts, regenerate school code, QR PNG/PDF, NLP toggle, delete report with reason.

Every staff action that touches a report writes an `access_logs` row. There is no route that updates or deletes `access_logs`.

---

## 4. Jobs and scheduled tasks

- `ClassifyReport` (queued): only if `nlp_enabled`; call ML service with 10 s timeout; retry 3 times; store urgency, confidence, suggestion, model version. On final failure leave fields null.
- `SendEmergencyNotice` (after response): email all active `bk` users of the school. Subject and body contain **no report content**, only a link to the dashboard.
- `ApplyRetention` (daily): on/after the school's `retention_date`, delete archived reports (or reduce them to anonymous stats). Never touch open reports.
- Optional: email if model says `darurat` when `notify_on_model_emergency` is on.

---

## 5. Seed data

- One school: SMP Telkom Purwokerto, random code, `nlp_enabled = false`.
- One demo school (needed for the Compro multi-school objective).
- Default incident types from spec section 4.
- One staff account per role on the demo school for testing. Never seed real people's data.

---

## 6. Must-have automated tests

1. Staff from school A gets 404/403 on any report from school B.
2. Kesiswaan can't open a `masalah pribadi` report and never receives contact fields.
3. Operator can't read report content.
4. No route deletes a report for `bk`, `kesiswaan` or `wali_kelas`.
5. Reporter status response contains no urgency or model fields.
6. 6th report from the same device on the same day is rejected.
7. 6th wrong ticket code within 15 minutes is throttled.
8. `danger_flag` report triggers exactly one emergency email with no story text in it.
9. ML service down → report still saved and visible as unclassified.
10. Practice mode submit creates no `reports` row and sends no email.
11. `reports` table has no IP, user agent or exact time columns, and the ticket code isn't stored in plain text.

---

## 7. Suggested build order

| Week | Dates (2026) | Backend focus |
|---|---|---|
| 6 | 19–23 Oct | Repo, Laravel setup, migrations, seeders, auth + 2FA, school-code routing |
| 7 | 26–30 Oct | Report submit end to end: validation, Turnstile, quota, ticket, encryption, status check |
| 8 | 2–6 Nov | Dashboard queries, filters, status, notes, assign, access log, emergency email |
| 9 | 9–13 Nov | Messages, Kesiswaan scope, wali kelas forwards, iseng/archive, practice mode → **MVP on staging** |
| 10–11 | 16–27 Nov | Pelajari announcements, stats, retention, operator panel, QR, security tests, bug fixes → **freeze for SMP Telkom** |
| 10–13 | 16 Nov–11 Dec | ML integration behind `nlp_enabled` (Compro) |
