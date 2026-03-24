# Quran Academy Online Portal – Implementation Phases (Condensed)

## Foundation Phases

### Phase 0: Project Foundation
- Architecture setup, shared Blade layout, audit/logging baseline, timezone strategy.
- Output: stable skeleton for modular feature development.

### Phase 1: Authentication and RBAC
- Login/reset/session handling, role-based redirects, permission-protected route groups.
- Output: secure role boundaries for all future modules.

### Phase 2: Master Data
- Teacher/student/supervisor/parent records, unique ID generation, DataTables CRUD/search.
- Output: reliable operational data base for scheduling and finance.

## MVP Phases

### Phase 3: Scheduling Core
- Slot model, class assignments, supervisor controls, reschedule request flow.
- Enforce one-to-one 30-minute teaching constraints.

### Phase 4: Attendance Core
- Separate admin-controlled attendance page (digits-only ID input).
- Teacher login/logout attendance events + class-level student attendance.
- Non-blocking portal use when attendance not marked; reporting must flag missing login/logout.

### Phase 5: Lesson Summaries and Tasks
- Summary submission workflow with immutability.
- Admin-only override with audit logging.
- Teacher task assignment + teacher-marked completion + parent visibility.

### Phase 6: Leave Workflow
- 12 annual leaves, appointment-date cycle, paid/unpaid support, leave types, medical attachments.
- Supervisor first review, Admin final authority, HR monitoring views.

### Phase 7: Salary and Advance Core
- Late grace/deduction rules, per-minute deduction from base salary, no deduction cap.
- Student absence does not reduce teacher pay.
- Advance salary deduction from next salary cycle.

### Phase 8: Invoicing and Parent Finance Visibility
- Monthly flat tuition model.
- Auto-generated invoices/vouchers.
- GBP default, USD selectable, currency stored per billing record.

### Phase 9: Notifications and Core Reports
- Admin-selectable notification channels (portal/email/WhatsApp model).
- Violation/policy alert capability (short portal + detailed email).
- Core exports for attendance, academics, leave, salary, invoices.

## Advanced / Later Phases

- MCB live payment gateway integration and reconciliation.
- WhatsApp provider implementation with delivery state tracking.
- Accountant portal expansion and final permissions.
- Payment failure and refund lifecycle.
- Tax source/update process and rounding policy.
- Memorization/progress rubric and advanced reporting.

## Dependency Order Notes

- RBAC must be complete before operational modules.
- Master data must be stable before scheduling and invoicing.
- Scheduling must exist before attendance, lesson summaries, and most reports.
- Attendance + leave outcomes are prerequisites for accurate salary processing.
- Invoice automation depends on fee/currency baseline and parent-student linking.

## Postponement Guidance

Do not block MVP on unresolved items:
- Accountant final scope
- Refund/failure workflows
- Tax configuration details
- Currency rounding rules
- Exact invoice due-date policy
- Strict alternative advance salary approval chain

Treat these as controlled later-phase refinements.
