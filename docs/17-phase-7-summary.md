# Phase 7 Summary — Communications, Notices, Reports & Exports

## What was implemented

### Portal notifications (database)

- Laravel `notifications` table migration and standard `database` channel usage via `App\Notifications\PortalAlert`.
- `NotificationController` for inbox listing, mark one read, mark all read.
- Topbar dropdown (Alpine.js) with unread badge and recent items; link to full history.
- **System hooks** (non-intrusive): new class schedule → teacher & student users; bulk invoice generation → student + linked parents; student marked absent → student/parents (+ supervisors if teacher offered reassignment); admin final leave decision → requester.

### Multi-channel architecture

- **`App\Notifications\PortalAlert`**: `via()` maps `portal`/`database` → `database`, `email`/`mail` → `mail`, `whatsapp` → custom channel name `whatsapp`.
- **`config/notifications.php`**: `system_channels` (comma-separated env `SYSTEM_NOTIFICATION_CHANNELS`, default `database`), `system_email_enabled` (`SYSTEM_NOTIFICATION_EMAIL`), WhatsApp flags `WHATSAPP_NOTIFICATIONS_ENABLED` and `WHATSAPP_LOG_ONLY` (default `true`).
- **`App\Notifications\Channels\WhatsAppChannel`**: registered in `AppServiceProvider` with `Notification::extend('whatsapp', ...)`.
- **`WhatsAppNotificationRecorder`**: writes **activity log** entries for intended WhatsApp payloads (integration-ready; no live API).

### Email

- `StaffNoticeMail` + `resources/views/emails/staff-notice-html.blade.php` for full notice body when **email** channel is selected.
- `PortalAlert::toMail()` for shorter system/staff-alert emails when mail is in the channel list.
- **Live sending** requires valid `.env` mail settings (`MAIL_*`). If mail is misconfigured, queued/sync failures may occur when email is selected; portal + log-only WhatsApp remain safe defaults.

### Staff / policy / violation notices

- Tables: `staff_notices`, `staff_notice_target_roles`, `staff_notice_target_users`, `staff_notice_reads`.
- **`StaffNoticeDispatchService`**: resolves recipients (`all_staff`, `roles`, `users`), sends portal DB notifications (short alert + link), optional `StaffNoticeMail`, optional WhatsApp channel (logged).
- **Admin UI** (`notifications.manage`, `Admin` role): CRUD-lite — index, create, show, delete at `admin/staff-notices/*`.
- **Staff UI**: list + show at `staff-notices/*` (roles: Admin, HR, Supervisor, Teacher, Accountant); read tracking via `staff_notice_reads`.

### Reports

- **Hub**: `GET admin/reports` (`reports.view`).
- **Operational** (Admin, Supervisor, HR + `reports.view`): employee attendance, class sessions, **new** student class attendance — each with filters and **Excel + PDF** export routes.
- **Financial** (Admin, Accountant + `reports.view`): tuition invoice overview with outstanding total + exports.
- **Payroll** (`reports.view` + `can:salary.manage`): monthly salary record listing + exports (sensitive).
- **Teacher self-service** (`Teacher` + `reports.view`): `GET my-classes/my-reports` — own attendance sample, lesson summaries, homework counts.

### Exports

- **Excel**: `maatwebsite/excel` — `App\Exports\*` (`EmployeeAttendanceEventsExport`, `StudentAttendanceReportExport`, `FinancialOverviewExport`, plus anonymous export for payroll).
- **PDF**: `barryvdh/laravel-dompdf` — `Pdf::loadView('reports.pdf.*')`.
- Exports respect query-string filters where implemented; activity log event `report.export` records format and filters.

### RBAC updates

- `reports.view` granted to **HR**, **Supervisor**, and **Teacher** in `RoleAndPermissionSeeder` (Admin already had full set; Accountant already had `reports.view`).
- **Accountant** added to `admin` route group middleware so `/admin/dashboard`, billing, and financial reports are reachable without exposing scheduling CRUD (still gated by `role:Admin|Supervisor` where applicable).

### Audit / activity

- Staff notice dispatch: `staff_notice.dispatched`.
- Notification read / read-all: `notification.read`, `notification.read_all`.
- WhatsApp channel: `notification.whatsapp_prepared`.
- Report exports: `report.export`.

## Live vs integration-ready

| Capability | Status |
|------------|--------|
| Portal DB notifications | Live (after migration) |
| Staff notices in portal | Live |
| Admin notice composer | Live |
| Email for staff notices / system mail | **Ready** — needs working `MAIL_*` |
| WhatsApp delivery | **Not live** — channel logs via activity log; flip `WHATSAPP_LOG_ONLY` / provider later |
| Excel / PDF exports | Live (packages installed) |

## Files added or touched (high level)

- **Migrations**: `2026_03_29_100000_create_notifications_table.php`, `2026_03_29_100001_create_staff_notices_table.php`
- **Models**: `StaffNotice`, `StaffNoticeTargetRole`, `StaffNoticeTargetUser`, `StaffNoticeRead`
- **Notifications / mail / channels**: `PortalAlert`, `WhatsAppChannel`, `StaffNoticeMail`
- **Services**: `SystemNotificationDispatcher`, `StaffNoticeDispatchService`, `WhatsAppNotificationRecorder`
- **Controllers**: `NotificationController`, `Admin\StaffNoticeController`, `Portal\StaffNoticeController`, `Admin\ReportHubController`, `Admin\StudentAttendanceReportController`, `Admin\FinancialOverviewReportController`, `Admin\PayrollSummaryReportController`, `Teacher\MyReportsController`; extended `EmployeeAttendanceReportController`, `AcademicSessionReportController`, `AdminDashboardController`, `App\DashboardController`, plus hooks in `ClassScheduleController`, `InvoiceController`, `LeaveFinalController`, `ClassSessionController`
- **Policies**: `StaffNoticePolicy`, `DatabaseNotificationPolicy` (explicit Gate binding for vendor model)
- **Routes**: `routes/notifications.php`, expanded `routes/admin.php`, `routes/teacher.php`, `routes/web.php`
- **Views**: notifications inbox, admin/portal staff notices, reports hub, new report screens, PDF layouts under `resources/views/reports/pdf/`, topbar/sidebar/dashboard updates
- **Config**: `config/notifications.php`

## Assumptions

- Academy size keeps report/export row limits (300–400) reasonable without streaming.
- Spatie roles for “staff” in `all_staff` mode: Admin, HR, Supervisor, Teacher, Accountant.
- Existing Breeze feature tests that expect `/register`, `/profile`, etc. may fail if those routes are intentionally omitted from this app; not introduced in Phase 7.

## Next recommendations

- Configure production `MAIL_*` and test staff notice with **email** channel.
- Add a real WhatsApp provider behind `WhatsAppChannel` when credentials exist; keep logging for audit.
- Optional: scheduled command for **payment reminders** calling `SystemNotificationDispatcher` with dedicated copy.
- Re-seed or run permission sync in deployed environments: `php artisan db:seed --class=RoleAndPermissionSeeder` (or equivalent) so HR/Supervisor/Teacher receive `reports.view`.
