# Phase 8 — Finalization, Hardening & Production Readiness

## Summary of improvements

Phase 8 focused on **stability, security, performance of listings, operational UX, and configuration** without adding new business domains. Deliverables include a **database-backed settings module**, **role-scoped global search**, **safer leave attachments**, **server-side pagination** (replacing client-side DataTables on large master lists), **dashboard snapshots** by role, **login routing fix for Accountants**, and **documentation** for backups and deployment.

---

## Bugs fixed / behaviour corrected

| Area | Change |
|------|--------|
| **Accountant login redirect** | Users with role `Accountant` are sent to `admin.dashboard` (same internal shell as other staff), matching their access to `/admin/*` billing and reports. |
| **Master data lists** | Teachers, students, and parents indexes used unbounded `get()` plus jQuery DataTables, loading every row in the browser. Replaced with **Laravel pagination (40/page)** and consistent table styling. |
| **Settings vs config** | Portal display name and invoice prefix were hard-coded; they are now **admin-configurable** (with sane fallbacks if the DB is empty). |

---

## Optimizations

- **Admin indexes**: `TeacherController`, `StudentController`, and `AcademyParentController` now use `paginate(40)->withQueryString()` instead of loading full collections.
- **Removed per-page jQuery + DataTables** on those three screens (fewer assets, faster first paint for large datasets).
- **Global search**: Capped at **12 results per category**; uses `LIKE` with escaped `%`/`_`; sections respect **policy `viewAny`** (and students for teachers via `search.global` + Teacher role without granting full `student.view`).
- **Settings**: Values cached in `SettingsService` for **10 minutes**; cache cleared on update.

---

## Security improvements

| Item | Detail |
|------|--------|
| **Login throttling** | Existing `LoginRequest` rate limit (per email + IP) retained; **additional** `throttle:12,1` on `POST /login` to cap burst attempts. |
| **Medical leave uploads** | Rules extended with **`mimetypes`** plus **post-validation MIME check** (`application/pdf`, `image/jpeg`, `image/png`). Files stored as **`UUID.ext`** under `leave_attachments` on the `local` disk (no original filename in path). |
| **Global search** | Protected by `auth` + **`permission:search.global`**; each result type gated by existing **policies** (`Teacher`, `Student`, `AcademyParent`, `Invoice`). |
| **System settings** | `GET/PUT admin/settings` under **`role:Admin`** + **`permission:settings.manage`**; updates logged via **activity** (`settings.updated`). |

---

## New / updated modules

### System settings (`settings` table)

- Keys: `system_name`, `default_currency` (GBP/USD/EUR/PKR), `default_timezone`, `invoice_number_prefix`.
- **UI**: `admin/settings` (edit form).
- **Integration**: `InvoiceNumberService` builds numbers as `{PREFIX}-{YEAR}-{SEQ}` using the stored prefix (sanitized to alphanumeric).
- **Branding**: `portalDisplayName` shared to `layouts.app`, `layouts.auth`, and topbar via view composers (safe `try/catch` if DB unavailable).

### Global search

- **Route**: `GET /search` → `GlobalSearchController` (named `search`).
- **UI**: Topbar quick search (for users with `search.global`) + full results page.
- **Permission**: `search.global` assigned to Admin (via full set), HR, Supervisor, Teacher, Accountant — **not** Student/Parent.

### UI / UX

- **`x-empty-state`** Blade component for empty filtered lists (teachers/students/parents).
- Table header styling aligned (gray header row) on the three master lists.
- Role-specific **dashboard** widgets on `dashboard` for Teacher, Parent, and Student; **admin** dashboard adds **quick stats** (active teachers/students/schedules) for Admin/HR/Supervisor.

---

## Final architecture overview (high level)

- **Monolithic Laravel 12** app with **Spatie permission** + **policies** on models.
- **Phase 8** adds a thin **settings layer** (`Setting` model + `SettingsService`) and a **read-mostly search layer** (`GlobalSearchService`) without changing core domain models.
- **Notifications / billing / scheduling** flows from earlier phases are unchanged in structure; only **invoice number format** and **display name** can reflect settings.

---

## Deployment readiness checklist

- [ ] `php artisan migrate --force`
- [ ] `php artisan db:seed --class=SettingsSeeder` (or full `DatabaseSeeder` on fresh env)
- [ ] `php artisan db:seed --class=RoleAndPermissionSeeder` (to apply `search.global` and any permission tweaks)
- [ ] `php artisan config:cache` / `route:cache` / `view:cache` in production after verifying `.env`
- [ ] Set `APP_DEBUG=false` in production
- [ ] Configure `MAIL_*` if email channels are used
- [ ] Ensure `storage/` and `bootstrap/cache/` are writable; run `php artisan storage:link` if public disk used
- [ ] Restrict `local` disk for leave attachments (not publicly web-served); downloads only through authorized controller action

---

## Backup & recovery (basic)

- **Database**: Schedule regular logical dumps (e.g. `mysqldump` / managed provider snapshots). Store off-server.
- **Files**: Back up `storage/app/leave_attachments` (and any future upload roots) with the same cadence as the DB.
- **Secrets**: Keep `.env` out of VCS; document restore order: DB → files → `php artisan migrate` if needed.

No automated backup job was added in code (optional future: scheduled Artisan command wrapping vendor tools).

---

## Known limitations

- **Default timezone** setting is stored for **reference and future use**; the running PHP `app.timezone` is **not** dynamically switched per request (avoids subtle date bugs across the app in this phase).
- **Outstanding tuition** on the admin dashboard still aggregates in PHP for accuracy with existing `balanceOutstanding()` logic; very large invoice volumes may warrant a SQL aggregate later.
- **Global search** is `LIKE`-based, not full-text search; adequate for typical academy sizes.
- **Default Breeze feature tests** that expect `/register`, `/profile`, etc. may still fail if those routes are intentionally absent — unrelated to Phase 8 features.

---

## Next recommendations (post–Phase 8)

- Optional **MySQL full-text** or **Laravel Scout** if search latency grows.
- Apply **`config('app.timezone')`** from settings in a single documented bootstrap path if product owners require it.
- **Automated DB backup** command + scheduler on the host or CI.
- Expand **`x-empty-state`** usage to more report pages for consistency.
