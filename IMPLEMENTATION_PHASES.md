# Quran Academy Online Portal – Implementation Phases

## 1. Execution Strategy

- Build as a modular monolith in Laravel 12 with clear domain boundaries: Identity/RBAC, Academic Operations, Scheduling/Attendance, HR/Leave, Finance, Notifications, and Reporting.
- Deliver in strict phases so that business-critical workflows become usable early (scheduling, attendance, lesson summary, leave, salary) before complex integrations.
- Use confirmed rules in `PROJECT_SRS_ANALYSIS.md` as source of truth; unresolved policies remain explicitly deferred.
- Prioritize data integrity and auditability first: immutable lesson summaries (admin override only with logs), leave final authority by admin, attendance event integrity, and salary traceability.
- Keep UI pragmatic with Blade + jQuery + DataTables; avoid early over-engineering or SPA complexity.
- Enforce role-safe route/controller boundaries from day one using Spatie permissions, policies, and role dashboards.

---

## 2. MVP Definition

MVP is the smallest production-usable system that can run daily academy operations end-to-end:

- Role-based login and portal access (Admin, HR, Supervisor, Teacher, Student, Parent).
- Teacher/student master records with unique IDs and searchable tables.
- Slot-based class scheduling and supervisor reassignment workflows.
- Separate admin-controlled attendance page using digits-only ID entry; attendance remains non-blocking for class management.
- Student attendance marking and absence handling.
- Lesson summary submission with immutability and admin-only override trail.
- Leave request chain (supervisor review + admin final decision), annual leave cycle by appointment date, paid/unpaid logic.
- Salary computation with grace-period late rules and deduction by full late duration once threshold is crossed.
- Monthly flat tuition invoices with auto-generation baseline and parent visibility.
- Basic notifications (portal + email initially) with per-message channel selection model.
- Foundational reports for attendance, lesson activity, leave, salary, and invoicing status.

MVP should be completed before advanced payment gateway, WhatsApp automation, detailed accountant expansion, and unresolved policy areas.

---

## 3. Recommended Development Phases

### Phase 0 – Foundation and Project Skeleton

- **Objective**
  - Prepare architecture, environments, coding conventions, and non-functional foundations needed by every module.
- **Included modules**
  - Project structure, shared layout, logging, timezone handling baseline, audit scaffolding, settings scaffolding.
- **Included business rules**
  - User-local timezone support strategy defined.
  - Audit-first policy for sensitive write actions established.
- **Dependencies**
  - None (starting point).
- **Likely database entities/tables**
  - `settings`, `audit_logs` (or activity log table), optional `timezones`.
- **Likely backend structure**
  - Base service layer conventions, middleware registration, exception handling, helper utilities.
- **Likely frontend/blade structure**
  - Master layouts, role dashboard shell templates, shared components for table/filter/status.
- **Risks/cautions**
  - Skipping conventions now creates inconsistent controllers/routes later.
- **Out of scope for this phase**
  - No feature workflows, no scheduling, no attendance, no finance logic.

### Phase 1 – Identity, RBAC, and User Lifecycle Core

- **Objective**
  - Implement secure authentication and role-scoped access as the control gate for all future work.
- **Included modules**
  - Authentication, forgot password, role-based redirection, User Management core.
- **Included business rules**
  - Strict role access boundaries (Supervisor/HR finance restrictions).
  - Parent can be linked to multiple students (data model enabled).
  - Accountant role recorded as planned but with restricted placeholder access.
- **Dependencies**
  - Phase 0.
- **Likely database entities/tables**
  - `users`, `password_reset_tokens`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `parent_student` pivot.
- **Likely backend structure**
  - `Auth` controllers, role middleware, policies, user/role management controllers and services.
- **Likely frontend/blade structure**
  - Login/forgot password pages, user management DataTable, role assignment forms.
- **Risks/cautions**
  - Permission leakage if routes are not policy-protected from start.
- **Out of scope for this phase**
  - No attendance, scheduling, lesson summary, leave, salary, invoicing workflows.

### Phase 2 – Master Data Setup (HR/Admin Core Records)

- **Objective**
  - Build reliable teacher, student, supervisor, parent master profiles with unique IDs and searchable listings.
- **Included modules**
  - Teacher Management, Student Management, Parent linkage, HR registration pages.
- **Included business rules**
  - Auto-generated unique IDs (`TCH-xxxx`, `STD-xxxx`) for profiles.
  - Active/inactive lifecycle status support.
  - Parent-to-multiple-students relationship support.
- **Dependencies**
  - Phase 1.
- **Likely database entities/tables**
  - `teachers`, `students`, `parents`, `supervisors` (or role-scoped profiles), `parent_student`.
- **Likely backend structure**
  - Profile CRUD controllers/services, ID generator service, validation requests, searchable endpoints.
- **Likely frontend/blade structure**
  - HR/Admin forms and DataTables (search by name/ID/email/status).
- **Risks/cautions**
  - ID collisions if generation is not transaction-safe.
- **Out of scope for this phase**
  - No class operations, leave approvals, salary, invoice generation.

### Phase 3 – Scheduling and Academic Session Backbone

- **Objective**
  - Enable supervisor-managed slot scheduling as the operational heart of daily classes.
- **Included modules**
  - Class Scheduling, Supervisor schedule operations, teacher availability/replacement basics.
- **Included business rules**
  - One-to-one sessions; 30-minute slots.
  - No mid-slot reassignment for already running classes.
  - Reassignment only to free/empty compatible slots per confirmed policy.
- **Dependencies**
  - Phase 2.
- **Likely database entities/tables**
  - `time_slots`, `class_schedules`, `class_sessions`, `teacher_availability`, `reschedule_requests`.
- **Likely backend structure**
  - Supervisor scheduling controllers, slot conflict validators, reassignment services.
- **Likely frontend/blade structure**
  - Supervisor scheduling board/table, teacher replacement page, student reschedule request views.
- **Risks/cautions**
  - Conflict detection and timezone display must be consistent.
- **Out of scope for this phase**
  - No payroll deduction computation, no invoice/payment workflows.

### Phase 4 – Attendance Domain (Separate Attendance Page + Class Attendance)

- **Objective**
  - Implement both attendance streams: dedicated sign-in/out attendance events and per-class student attendance.
- **Included modules**
  - Attendance Management, dedicated attendance terminal page, student absence signaling.
- **Included business rules**
  - Separate admin-controlled attendance page, digits-only ID entry.
  - Portal class operations remain non-blocking if attendance sign-in/out missing.
  - Missing login/logout must be visible in reporting.
  - Student absent -> teacher can mark availability -> supervisor notified.
- **Dependencies**
  - Phase 3 (schedule context).
- **Likely database entities/tables**
  - `attendance_events` (login/logout), `attendance_days`, `student_class_attendance`, optional `availability_flags`.
- **Likely backend structure**
  - Attendance capture controllers, late-minute calculators, event validators, reporting query services.
- **Likely frontend/blade structure**
  - Public/internal attendance page (ID input), teacher class attendance forms, supervisor alerts.
- **Risks/cautions**
  - Device/time clock drift impacts late calculations; use normalized server timestamps.
- **Out of scope for this phase**
  - No final salary payout approval screens yet.

### Phase 5 – Lesson Summary and Homework/Task Tracking

- **Objective**
  - Lock in academic evidence trail for each class and assign/track student tasks.
- **Included modules**
  - Lesson Summary, Homework & Task.
- **Included business rules**
  - Lesson summary required after class.
  - Submission allowed immediate, same day, or next day grace.
  - Summary immutable after submission; admin-only override with full audit logging.
  - Teacher marks task completion status; parent can view completion outcomes.
- **Dependencies**
  - Phases 3 and 4.
- **Likely database entities/tables**
  - `lesson_summaries`, `lesson_summary_overrides`, `tasks`, `task_status_logs`.
- **Likely backend structure**
  - Teacher lesson/task controllers, admin override controller, immutable-state guards, audit hooks.
- **Likely frontend/blade structure**
  - Teacher summary form, task assignment list, parent task monitoring panels.
- **Risks/cautions**
  - Immutability must be enforced at policy/service level, not only UI.
- **Out of scope for this phase**
  - No scoring rubric implementation beyond basic fields (still open).

### Phase 6 – Leave Management and Approval Chain

- **Objective**
  - Deliver complete leave lifecycle with supervisor review and admin final authority.
- **Included modules**
  - Leave Management, HR leave monitoring page, leave history/count views.
- **Included business rules**
  - 12 annual leaves by appointment-date cycle.
  - Paid/unpaid flag required; leave types include Medical/Casual/Emergency/Unpaid.
  - Supervisor first review with optional comments.
  - Admin final decision overrides supervisor action.
  - Medical attachment support.
- **Dependencies**
  - Phase 2 (employee profiles), Phase 4 (attendance interactions).
- **Likely database entities/tables**
  - `leave_requests`, `leave_reviews`, `leave_balances`, `leave_attachments`, `leave_policies`.
- **Likely backend structure**
  - Multi-step approval services, leave cycle calculator, balance renewal scheduler.
- **Likely frontend/blade structure**
  - Teacher leave request form, supervisor queue, admin final approval board, HR monitoring dashboard.
- **Risks/cautions**
  - Incorrect cycle reset logic (appointment-based) can corrupt balances.
- **Out of scope for this phase**
  - No strict alternative advance-salary chain enforcement beyond baseline.

### Phase 7 – Salary and Advance Salary Core

- **Objective**
  - Produce auditable monthly salary computations and admin approval/recording flow.
- **Included modules**
  - Salary Management, Advance Salary Requests (baseline), teacher salary view.
- **Included business rules**
  - Late grace: <=15 min no deduction; >=16 min deduct full late duration.
  - Per-minute deduction derived from base salary; no cap.
  - Student absence does not reduce teacher salary for that slot.
  - Approved advance salary deducted from next salary.
- **Dependencies**
  - Phases 4 and 6.
- **Likely database entities/tables**
  - `salary_periods`, `salary_components`, `salary_deductions`, `salary_adjustments`, `salary_payments`, `advance_salary_requests`.
- **Likely backend structure**
  - Salary calculator services, payroll run commands, admin salary approval controllers.
- **Likely frontend/blade structure**
  - Admin salary breakdown and approval pages, teacher read-only salary details page.
- **Risks/cautions**
  - Payroll recalculation rules must be idempotent and versioned by period.
- **Out of scope for this phase**
  - No expanded accountant-only payroll permissions (still open).

### Phase 8 – Tuition Billing, Monthly Invoices, Parent Finance Views (MVP Finance)

- **Objective**
  - Implement monthly flat-fee billing operations and parent-facing invoice/payment status visibility.
- **Included modules**
  - Payment & Invoice baseline, parent payment history, student payment status.
- **Included business rules**
  - Monthly flat tuition model.
  - Auto-generation of invoices/vouchers.
  - Currency stored per record; GBP default, USD selectable.
  - International fee payment scope prioritized.
- **Dependencies**
  - Phase 2 (student/parent), Phase 1 (roles), Phase 0 (scheduler/cron infrastructure).
- **Likely database entities/tables**
  - `fee_plans`, `invoices`, `invoice_items`, `invoice_status_logs`, `payment_records`, `currencies`.
- **Likely backend structure**
  - Invoice generation command/services, parent payment status controllers, finance summary services.
- **Likely frontend/blade structure**
  - Parent invoice list/detail, payment status pages, admin invoice management DataTables.
- **Risks/cautions**
  - Keep unresolved tax and due-date policies configurable, not hard-coded.
- **Out of scope for this phase**
  - No full payment gateway callbacks/refund/failure automation yet.

### Phase 9 – Notification Center and Operational Reporting (MVP Completion)

- **Objective**
  - Add event-driven communication and management reporting needed for production operation.
- **Included modules**
  - Notification Module, Reports Module (core set), Violation/Policy alert messaging.
- **Included business rules**
  - Admin selects channels per notification (portal/email/WhatsApp model).
  - Violations/policy alerts support short portal notification plus detailed email content.
  - Reports must include missing attendance states (did not login/logout).
- **Dependencies**
  - Phases 3–8 (event sources).
- **Likely database entities/tables**
  - `notifications`, `notification_deliveries`, `notification_templates`, `violations`, reporting materialized tables (optional).
- **Likely backend structure**
  - Event listeners, notification dispatcher service, report controllers/export services.
- **Likely frontend/blade structure**
  - Admin notification composer, inbox/alerts panels, report filters/export pages.
- **Risks/cautions**
  - Queue/retry behavior must be reliable before enabling multi-channel sends.
- **Out of scope for this phase**
  - No deep analytics or advanced BI-style report warehouse.

---

## 4. Advanced / Later Phases

- **Phase 10 – Payment Gateway and Transaction Robustness**
  - Live MCB gateway integration, webhook/callback validation, failure handling states, reconciliation.
- **Phase 11 – WhatsApp Delivery Integration**
  - Production WhatsApp provider, delivery receipts, retries, template governance.
- **Phase 12 – Advanced Finance and Accountant Expansion**
  - Final accountant portal pages/permissions, transaction recording policies, possible salary transaction scope.
- **Phase 13 – Tax, Refund, and Billing Policy Refinement**
  - Tax source/update workflow, refund lifecycle, payment failure policies, rounding policy enforcement.
- **Phase 14 – Academic Intelligence Enhancements**
  - Formal memorization progress rubric/scoring model, richer progress analytics.
- **Phase 15 – Advanced Reporting and Compliance**
  - High-volume exports, audit dashboards, compliance packs, operational KPI dashboards.

---

## 5. Cross-Module Dependency Notes

- Scheduling must exist before student attendance, lesson summaries, and most supervisor workflows.
- Separate attendance events must exist before accurate late deductions and payroll.
- Leave approval outcomes feed salary deductions; payroll should not be finalized before leave resolution.
- Master profile data (teachers/students/parents) must be stable before invoicing and parent portal finance views.
- Invoice generation should run only after fee plans and currency model are finalized at baseline level.
- Notification triggers are meaningful only after core domain events are implemented.
- Reporting should follow stable schemas; avoid building heavy reports before core transactional flows are settled.

---

## 6. Risks and Planning Notes

- **Architectural risk**
  - Blending feature logic directly in controllers can make policy-heavy workflows brittle; keep services/policies explicit.
- **Workflow risk**
  - Admin-final leave authority and supervisor-first review can be misapplied without strict status state machine.
- **Data integrity risk**
  - Immutable lesson summaries with admin override need reliable audit diffs and actor tracking.
- **Financial risk**
  - Salary calculations depend on accurate attendance timestamps and leave cycle rules; timezone/clock consistency is critical.
- **Integration risk**
  - Payment failure/refund/tax and rounding are open; implement finance with configurable placeholders first.
- **Security/audit risk**
  - Role leakage into financial endpoints and weak audit trails can create compliance gaps.

---

## 7. Suggested First Build Sequence

1. Project foundation and shared architecture scaffolding.
2. Authentication, session handling, and password reset.
3. Roles/permissions matrix and route policy enforcement.
4. Admin/HR user setup and master profile onboarding (teacher/student/supervisor/parent).
5. Unique ID generation and searchable DataTables for master records.
6. Scheduling engine (time slots, assignments, reschedule requests, controlled reassignment).
7. Separate attendance page and attendance event tracking.
8. Per-class student attendance and absence availability workflow.
9. Lesson summary (immutable) and admin override audit flow.
10. Homework/task assignment and teacher-marked completion with parent visibility.
11. Leave management chain (supervisor review, admin final, HR monitoring, paid/unpaid + attachments).
12. Salary engine (late grace logic, per-minute deduction, student-absence compensation, advance deduction).
13. Monthly flat-fee invoice generation and parent finance visibility (GBP/USD record support).
14. Notification center with channel selection and violation/policy alerts.
15. Core reports and exports for attendance, academics, leave, salary, and invoices.
