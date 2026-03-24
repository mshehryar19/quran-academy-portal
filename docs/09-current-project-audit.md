# Current Project Audit

## 1. Project Snapshot

- **Framework version**: Laravel 12 (`laravel/framework:^12.0`), PHP `^8.2`.
- **Overall state**: Mostly fresh Laravel boilerplate with planning documentation added.
- **Customization level**:
  - Planning docs are customized (`PROJECT_SRS_ANALYSIS.md`, `IMPLEMENTATION_PHASES.md`, `docs/01`-`08`).
  - Application/runtime code is still near-default starter state.

## 2. Existing Structure Review

### `app/`
- Present: `Http/Controllers/Controller.php`, `Models/User.php`, `Providers/AppServiceProvider.php`.
- Missing (for roadmap): custom controllers, requests, services/actions, policies, middleware extensions, module folders.

### `bootstrap/`
- `app.php` is default minimal bootstrap.
- `providers.php` only registers `AppServiceProvider`.
- No custom middleware pipeline setup yet.

### `config/`
- Standard Laravel config files only (`app`, `auth`, `database`, `filesystems`, `logging`, `mail`, etc.).
- No custom domain config (attendance, salary, leave cycle, notification channels, billing policy).

### `database/`
- Migrations: only default `users`, `cache`, `jobs`.
- Seeders: only default `DatabaseSeeder` creating one test user.
- Factories: only `UserFactory`.
- `database.sqlite` exists (likely bootstrap artifact), while `.env` currently points to MySQL.

### `public/`
- Default Laravel files only (`index.php`, `.htaccess`, `robots.txt`, `favicon.ico`).

### `resources/views/`
- Only `welcome.blade.php` (default Laravel welcome page).
- No auth pages, no dashboards, no layout shells, no role folders.

### `routes/`
- Only `web.php` and `console.php`.
- `web.php` contains only root route (`/` -> `welcome`).
- No route groups for admin/hr/supervisor/teacher/student/parent.

### `storage/`
- Standard directories: `app`, `framework`, `logs`.
- No domain-specific upload folder conventions yet.

### `composer.json`
- Core packages only: Laravel framework + Tinker.
- No auth scaffolding package, no RBAC package, no activity-log/audit package, no reporting/export packages.

### `package.json`
- Vite + Tailwind stack only (`tailwindcss`, `axios`, `laravel-vite-plugin`, `concurrently`).
- No jQuery or DataTables packages installed yet.

## 3. Existing Functional Setup

### Authentication
- **Status**: Not scaffolded.
- Evidence: No login/register routes/controllers/views; route list shows only `/`, `/up`, and storage routes.
- Note: `welcome.blade.php` contains conditional links for login/register, but those routes do not currently exist.

### Authorization / Roles / Permissions
- **Status**: Not implemented.
- No Spatie roles/permissions package installed.
- No policy classes or permission matrix in code.

### Database Schema
- **Status**: Default starter schema only.
- Present: `users`, `password_reset_tokens`, `sessions`, `cache`, `jobs`.
- Missing: all academic, scheduling, attendance, leave, salary, billing, notification, and audit domain tables.

### Seeders / Factories
- **Status**: Minimal default only.
- No admin bootstrap seeder, no role seeder, no domain seeders.

### Layouts / Views
- **Status**: Not ready for portal.
- No master layout, sidebar/topbar partials, role-specific dashboards, or module views.

### Frontend Assets
- Tailwind + Vite is present by default.
- jQuery/DataTables readiness is **not present** yet.

### File Uploads
- Laravel filesystem config is default and usable as a base.
- No specific upload policies or paths for medical leave attachments yet.

### Notifications / Mail Readiness
- Mail config exists (default Laravel config; currently log mailer in environment).
- Notification channels (portal/email/WhatsApp strategy) are not implemented.

### Activity Logging / Audit Setup
- Default Laravel logging only (`storage/logs/laravel.log`).
- No domain activity/audit trail structure yet.

## 4. Gap Analysis

### What is already ready
- Laravel 12 runtime base and conventions.
- Basic user model/auth config foundation.
- Queue/cache/jobs infrastructure migrations available (useful for notifications/reports later).
- Planning documentation is strong and aligned with SRS + clarified rules.

### What is partially ready
- Filesystem and mail configs are available but need domain-specific hardening.
- Tailwind/Vite frontend pipeline exists but not aligned to Blade+jQuery+DataTables target yet.

### What is missing (major)
- Authentication scaffolding and role-based portal access.
- RBAC package + permission matrix + policy enforcement.
- All portal modules (scheduling, attendance, lesson summaries, leave, salary, invoices, notifications, reports).
- Modular Blade structure and route groups by role.
- Domain database foundation (master + transactional + audit tables).
- Audit logging model for immutable/approval-sensitive actions.

### What can be reused
- Default `users` and auth config as starting point.
- Existing queue/jobs tables for async notifications/reports.
- Existing filesystem/mail config templates.

### What should be postponed (as planned)
- Final Accountant module scope.
- Payment failure/refund workflow.
- Tax source/update and rounding policy.
- Strict alternative advance salary approval chain.
- Formal memorization scoring rubric.

## 5. Recommended Cleanup / Preparation

- Keep starter Laravel files for now; do not remove boilerplate until replacements exist.
- Replace default `welcome` entry flow with authenticated role portal flow in Phase 1.
- Create route groups early: `admin`, `hr`, `supervisor`, `teacher`, `student`, `parent`.
- Establish Blade architecture before feature pages:
  - `layouts/` master app layout,
  - shared partials/components (topbar/sidebar/alerts/table wrappers),
  - role/module view folders.
- Add RBAC package (Spatie) at start of Phase 1.
- Add jQuery + DataTables dependencies when master listing pages begin.
- Define storage strategy for secure medical attachment uploads (private disk + controlled access).
- Prepare `storage:link` and environment-specific filesystem usage rules for uploads.

## 6. Readiness for Phase 1

- **Authentication setup**: Base-ready, but scaffolding not implemented.
- **Roles & permissions**: Not ready until package + schema + seeders are added.
- **Admin seed/setup**: Not ready; requires role seeding and controlled initial admin user provisioning.
- **Dashboard layout**: Not ready; no master layout/partials.
- **Sidebar/topbar structure**: Not ready; should be introduced before module UIs.
- **Master data foundation**: Not ready; no domain profile tables or CRUD scaffolding.

Overall Phase 1 readiness: **Good for start**, but effectively a blank implementation slate.

## 7. Recommended Immediate Next Actions

1. Confirm Phase 1 technical decisions (auth approach, RBAC package, base route architecture).
2. Install and configure RBAC package (Spatie) with seed-ready permissions model.
3. Build authentication and role-based redirection baseline.
4. Create admin-first UI shell (`layouts`, sidebar/topbar, flash/error components).
5. Set up route groups and middleware boundaries for all planned roles.
6. Add foundational seeders:
   - roles/permissions,
   - initial admin user,
   - optional baseline reference data.
7. Introduce module folder structure for controllers/requests/services/views before writing feature logic.
8. Prepare database planning execution order from `docs/05-database-planning.md` and phase map.
9. Add audit strategy baseline (package or custom table approach) before immutable/approval features.
10. Add jQuery/DataTables dependencies when first list-heavy pages (master data) begin.

## 8. Risks / Cautions

- Starting feature work before RBAC may cause broad route/security rewrites later.
- Skipping audit foundation early risks non-compliant handling of:
  - lesson summary admin override,
  - leave/salary approvals,
  - policy/violation communications.
- Data model drift risk if migrations start without following phased domain boundaries.
- Current environment mismatch risk:
  - `.env.example` defaults to sqlite,
  - active `.env` uses MySQL;
  ensure team setup is standardized before collaborative development.
- jQuery/DataTables are required by project standards but not yet installed; delaying too long may lead to inconsistent UI patterns.
