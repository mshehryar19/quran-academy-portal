# Quran Academy Online Portal – Build Roadmap

## 1. Build Strategy

- Build in **foundation-first, admin-first, phased increments**.
- Start with security and control layers (auth, RBAC, policies, audit baseline) before feature workflows.
- Use admin-first setup to establish governance screens, approvals, and shared layout patterns that all other roles inherit.
- Build transactional modules only after prerequisites are stable (master data -> scheduling -> attendance -> leave/salary -> billing).
- Prevent rework by enforcing:
  - service-layer business rules,
  - policy-driven authorization,
  - append-only audit trails for sensitive actions.

## 2. Recommended Exact Build Order

1. Foundation prep and architecture scaffolding.
2. Authentication + RBAC + role route groups.
3. Admin bootstrap (initial admin + roles/permissions seed setup).
4. Shared Blade shell (layout, sidebar/topbar, role-aware nav).
5. Master data (teacher/student/parent/supervisor profiles + IDs).
6. Scheduling base (slots, assignments, controlled reassignment).
7. Attendance (separate attendance page + class student attendance).
8. Lesson summaries + task workflow.
9. Leave workflow (supervisor first review, admin final authority, HR monitoring).
10. Salary engine and payroll records (PKR).
11. Monthly fee vouchers/invoices and payment status (GBP default, USD optional).
12. Notifications + violation/policy notices.
13. Reports and exports.
14. Advanced integrations/refinements.

Dependency highlights:
- Scheduling requires master data + RBAC first.
- Attendance requires scheduling context.
- Leave/salary requires attendance + profile data.
- Invoices/payments require student/parent linkage and fee setup.

What can be mocked initially:
- External payment gateway callback handling.
- WhatsApp delivery transport.
- Advanced tax/refund/failure handling.

## 3. Foundation Setup Roadmap

1. Confirm technical standards from `docs/07-coding-standards.md`.
2. Add RBAC package and permission model strategy.
3. Define route groups: `admin`, `hr`, `supervisor`, `teacher`, `student`, `parent`.
4. Set up middleware/policies for role boundaries.
5. Create admin-first base layout and reusable partial shells.
6. Prepare seed strategy:
   - roles/permissions seeder,
   - initial admin seeder.
7. Prepare audit/logging baseline for sensitive actions.
8. Prepare upload/storage baseline for leave medical attachments.
9. Add early timezone preference placeholder in user/profile design for local-time display.
10. Establish config/lookup strategy for status enums and workflow states.

## 4. Database-First Planning Sequence

### Batch A: Identity and access (first)
- `users`
- roles/permissions tables
- optional `user_profiles` / staff profile base

### Batch B: Master people and mappings
- `teachers`, `students`, `parents`, `supervisors`
- `parent_student` mapping
- unique ID support fields for teacher/student

### Batch C: Scheduling and attendance foundations
- `class_slots`
- `class_schedules`
- `reschedule_requests`
- `attendance_events` (login/logout from separate page)
- `student_attendance`

### Batch D: Academic records
- `lesson_summaries` (immutable policy)
- `lesson_summary_overrides` (admin override log)
- `homework_tasks`
- `homework_task_status`

### Batch E: Leave and salary
- `leave_requests`
- `leave_reviews`
- `leave_balances`
- `leave_attachments`
- `salary_periods` / `salary_records`
- `salary_adjustments`
- `salary_payments`
- `advance_salary_requests`

### Batch F: Billing and notifications
- `fee_plans`
- `invoices` / `fee_vouchers`
- `invoice_items`
- `fee_payments`
- `notifications`
- `notification_deliveries`
- `violations` / `policy_notices`

### Batch G: Audit and reporting support
- `activity_logs` / domain audit tables
- report support tables/materialized summaries (if needed)

### Wait for later-phase clarity
- detailed accountant-specific tables
- refund/failure workflow tables
- detailed tax configuration automation
- strict alternative advance approval tables (if introduced)

## 5. Recommended First Backend Structure

- **Models**: start with user/role/profile entities, then scheduling and attendance models.
- **Form Requests**: create per module early for strict input validation.
- **Controllers**: thin controllers grouped by role/module.
- **Services/Actions**:
  - schedule assignment/reassignment,
  - attendance and late-minute computation,
  - leave state transitions,
  - salary calculation,
  - invoice generation.
- **Policies/Middleware**: enforce role and object-level checks from day one.
- **Seeders**: permissions + admin user + baseline lookup data.
- **Enums/Config lookups**: leave statuses/types, approval states, notification channels, currency codes.

## 6. Recommended First Frontend / Blade Structure

- Create base layout system first:
  - `layouts/app`,
  - role dashboard layout variants if needed.
- Add shared UI skeleton:
  - sidebar/topbar,
  - breadcrumb/header section,
  - flash/validation alert partials.
- Add role-aware navigation map based on permissions.
- Create shared table partials for DataTables pages.
- Create shared form partials for create/edit screens.
- Define modal conventions (confirm/reject/approve actions with reason fields).
- DataTables plan:
  - start on master lists (teachers/students),
  - use consistent search fields (name, ID, email, status),
  - move to server-side mode when data grows.

## 7. Module-by-Module Development Sequence

### 1) Authentication
- **Prerequisites**: foundation prep.
- **Key outputs**: login/reset/session + role redirection.
- **Why now**: all portal access depends on it.

### 2) Dashboards (base)
- **Prerequisites**: auth + RBAC.
- **Key outputs**: role landing pages and nav shell.
- **Why now**: stabilizes UI/UX baseline for all modules.

### 3) User/Employee foundation
- **Prerequisites**: RBAC + admin setup.
- **Key outputs**: people records, statuses, profile lifecycle.
- **Why now**: downstream modules depend on these identities.

### 4) Teacher management
- **Prerequisites**: user foundation.
- **Key outputs**: teacher records + IDs + listing.
- **Why now**: required for scheduling and attendance.

### 5) Student management
- **Prerequisites**: user foundation.
- **Key outputs**: student records + IDs + listing.
- **Why now**: required for scheduling and billing.

### 6) Parent linkage
- **Prerequisites**: student + parent records.
- **Key outputs**: one-parent-to-multiple-students mapping.
- **Why now**: required before parent portal and invoicing visibility.

### 7) Class slots
- **Prerequisites**: teacher/student records.
- **Key outputs**: slot master and availability context.
- **Why now**: scheduling depends on slot definitions.

### 8) Class scheduling
- **Prerequisites**: class slots + teacher/student records.
- **Key outputs**: assignments, reschedule, reassignment constraints.
- **Why now**: operational core for attendance and academics.

### 9) Attendance
- **Prerequisites**: scheduling.
- **Key outputs**: separate attendance page events + class attendance.
- **Why now**: needed for leave/salary logic and compliance tracking.

### 10) Lesson summaries
- **Prerequisites**: scheduling + attendance context.
- **Key outputs**: immutable summaries + admin override audit.
- **Why now**: core academic traceability.

### 11) Homework/tasks
- **Prerequisites**: teacher/student module baseline.
- **Key outputs**: task assignment + completion status visibility.
- **Why now**: complements lesson tracking and parent oversight.

### 12) Leave management
- **Prerequisites**: employee records + audit baseline.
- **Key outputs**: leave requests, supervisor review, admin final decision, HR monitoring.
- **Why now**: policy and salary dependency.

### 13) Salary management
- **Prerequisites**: attendance + leave + advance requests.
- **Key outputs**: payroll calculations, deductions, admin approvals, teacher view-only salary details.
- **Why now**: depends on prior operational data.

### 14) Invoices/fee vouchers
- **Prerequisites**: student/parent linkage + fee setup.
- **Key outputs**: monthly flat fee vouchers, payment status tracking.
- **Why now**: finance module after operational core is stable.

### 15) Notifications
- **Prerequisites**: core module events.
- **Key outputs**: channel-selectable notifications and policy notices.
- **Why now**: event coverage is useful once core workflows exist.

### 16) Reports
- **Prerequisites**: stable data across modules.
- **Key outputs**: attendance, academic, leave, salary, billing reports.
- **Why now**: report quality depends on mature transactional data.

### 17) Violations/policy notices
- **Prerequisites**: admin controls + notification base.
- **Key outputs**: portal + email policy communication records.
- **Why now**: aligns with admin governance once staff modules active.

### 18) Accountant module (future)
- **Prerequisites**: finalized requirements.
- **Key outputs**: dedicated finance role pages and permissions.
- **Why now**: deferred until scope is confirmed.

## 8. Currency and Financial Design Notes

### Student fee/payment currency
- Student fees are for international students.
- Student fee records may use:
  - **GBP** as default
  - **USD** as optional selectable currency
- Currency must be stored with student fee/invoice/payment records.

### Employee salary currency
- Employee salaries, deductions, advances, and payroll records must use **PKR**.
- Do not mix payroll currency with student fee currency.
- Late/leave salary deductions must be calculated and stored in PKR.

### Implementation implications
- Keep currency separation explicit in:
  - database schema,
  - UI labels/forms,
  - reports/exports,
  - service-layer business logic.

## 9. What Should Be Mocked or Deferred Initially

- MCB payment gateway live integration.
- WhatsApp channel provider integration.
- Advanced tax automation.
- Payment failure and refund workflows.
- Advanced accountant portal and permissions.
- Memorization/progress rubric engine.
- Exact invoice due-date/reminder policy refinements.
- Strict expanded advance salary approval chain (if required later).

## 10. Shared Reusable Architecture Recommendations

- Reusable Blade partials/components for:
  - layout shell,
  - nav blocks,
  - status badges,
  - approval action cards,
  - table wrappers,
  - modal patterns.
- Modular folder organization by domain and role.
- Route organization by role prefix + module naming.
- Form request validation for every write endpoint.
- Business rules in services/actions, not route closures or Blade.
- Centralized activity log strategy for approvals/overrides/financial mutations.
- Immutable record handling pattern (lock + admin override + audit trail).
- Approval workflow pattern as explicit state machine.
- Notification abstraction: channel dispatcher (portal/email/WhatsApp) with per-message selection.

## 11. Immediate Next Build Step

**Start Phase 1 implementation:** authentication + RBAC/permissions + initial admin setup + base role-aware layout shell (dashboard frame, sidebar/topbar, route groups, policy guards).

## 12. Risks / Cautions

- Building scheduling before master data and permissions are ready will cause major rewrites.
- Mixing student fee currency and payroll currency will break finance integrity.
- Weak RBAC early can expose financial or restricted operations.
- Placing heavy business logic in Blade/views will create maintenance and testing issues.
- Skipping audit design early will compromise immutable/approval-sensitive modules.
- Building advanced finance workflows before unresolved rules are finalized increases rework.
