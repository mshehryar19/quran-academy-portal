# PROJECT_SRS_ANALYSIS

## Project Overview

The Quran Academy Online Portal is a centralized, role-based operations platform for managing one-to-one Quran classes (1 teacher : 1 student : 30 minutes), academic tracking, attendance discipline, leave handling, salaries, tuition billing, and stakeholder communication (staff, students, parents, finance, and management).

Primary outcomes expected from the SRS:
- Operational control of daily class delivery and scheduling.
- Transparent teacher accountability (attendance, punctuality, lesson records, violations).
- Student learning continuity (lesson summaries, homework/tasks, progress visibility).
- Financial integrity (tuition invoicing/payments and teacher salary processing).
- Multi-role collaboration with strict data access boundaries.

---

## User Roles

Identified roles and their business intent:

1. **Admin**
   - Full system control.
   - Salary/payment governance, approvals, reports, system settings, user management.

2. **HR**
   - Teacher/supervisor/student onboarding and profile registration.
   - Handles/initiates advance salary requests workflow.
   - Restricted from finance visibility (salary details/payment records).

3. **Supervisor**
   - Owns schedule operations, class assignment, replacement handling, teacher oversight.
   - Handles operational approvals/escalations (leave forwarding, reschedule handling).
   - Restricted from financial data.

4. **Teacher**
   - Conducts classes, marks student attendance, submits immutable lesson summaries.
   - Assigns student tasks/homework, requests leave, views salary breakdown (read-only), receives violations.

5. **Student**
   - Views schedule, tasks, lesson summaries, progress, payment status.
   - Can request class reschedule.

6. **Parent**
   - Monitors child attendance/progress/lesson activity.
   - Views payment history, invoices, and pays tuition.

7. **Accountant**
   - Financial records management (tuition/salary transactions and accounting support).
   - Mentioned in role list; detailed page map is not explicitly defined in SRS (clarification needed).

---

## Core Modules

### 1) Authentication Module
- Login via email/username + password.
- Forgot password flow (email link reset).
- Session management + role-based post-login redirection.

### 2) Dashboard Module
- Role-customized dashboards:
  - Teacher: classes, statuses, pending summaries, leave, alerts/tasks.
  - Supervisor: active classes, teacher availability/leave, student attendance, reassignment alerts.
  - Admin: totals, revenue, pending salary and leave approvals, notifications.

### 3) Teacher Management Module
- Registration/profile lifecycle (primarily HR/Admin).
- Monitoring and searchable listing (with unique IDs).

### 4) Student Management Module
- Registration/profile lifecycle (primarily HR/Supervisor/Admin).
- Monitoring and searchable listing (with unique IDs).

### 5) Class Scheduling Module
- Slot-based assignment (30-minute slots).
- Supervisor controls assignment/modification/reassignment.

### 6) Attendance Management Module
- Teacher attendance (login/logout/late detection).
- Student attendance per class (present/absent).
- Absence-triggered slot availability and supervisor alert.

### 7) Lesson Summary Module
- Mandatory per class summary with fixed required fields.
- Immutable after submission.

### 8) Homework & Task Module
- Teacher task assignment to students.
- Student task visibility and completion follow-up via reports.

### 9) Leave Management Module
- Teacher annual leave tracking.
- Multi-stage approval workflow.
- Salary impact for exceeded leave allowance.

### 10) Salary Management Module
- Salary computation with deductions/adjustments.
- Approval and transaction recording by admin.
- Teacher view-only payslip details.

### 11) Payment & Invoice Module
- Tuition invoicing with tax fields (FBR + gov taxes).
- Online payments via MCB merchant integration.
- Parent visibility and payment execution.

### 12) Notification Module
- Event-based notifications via portal, email, WhatsApp.

### 13) Reports Module
- Academic, attendance, financial and performance reports.
- Export to PDF/Excel.

### 14) User Management Module
- Role assignment, status management, access governance.

### 15) Violation Management Module
- Violation generation, logging, and persistent visibility.

---

## Detailed Business Rules

### Teaching and Scheduling Rules
- Each class session is exactly **30 minutes**.
- Session cardinality is strictly **1 teacher : 1 student**.
- Teachers can have multiple slots/day.
- Multiple teachers can run classes in parallel.
- Slot vacancy can occur if a student is marked absent and may be reassigned by supervisor.

### Role Access Rules
- All actions must be permission-gated by role.
- Supervisors cannot access financial data.
- HR cannot view salary details/payment records.
- Teacher salary page is strictly view-only for teacher.

### Data Integrity Rules
- Teacher and student IDs are system-generated and unique.
- ID format examples:
  - Teacher: `TCH-0001`
  - Student: `STD-0001`
- IDs are used for identification, indexing, and search/filtering.

### Record Finality Rules
- Lesson summaries are immutable once submitted.
- Violations remain recorded in system history.

---

## Workflows and Approvals

### A) Teacher Leave Workflow
1. Teacher submits leave request.
2. Request goes to Supervisor or HR.
3. Supervisor forwards to Admin.
4. Admin approves/rejects.
5. Approved leave updates attendance and salary records.

Dependencies:
- Leave module -> Attendance module -> Salary module -> Notifications.

### B) Student Absence and Slot Reassignment Workflow
1. Teacher marks student absent for class.
2. Teacher marks class as available.
3. System notifies supervisor.
4. Supervisor may assign another student/teacher-slot usage accordingly.

Dependencies:
- Attendance module -> Scheduling module -> Notification module.

### C) Teacher Replacement Workflow
1. Teacher absence identified.
2. Supervisor checks available teachers.
3. Supervisor assigns replacement.
4. Replacement teacher gets automatic notification.

Dependencies:
- Leave/Attendance -> Scheduling -> Notifications.

### D) Student Reschedule Request Workflow
1. Student submits request.
2. Supervisor reviews.
3. Supervisor updates schedule.

Dependencies:
- Student portal -> Supervisor scheduling -> Notifications.

### E) Salary Processing Workflow
1. System computes monthly salary from policy inputs.
2. Admin reviews breakdown.
3. Admin approves and records payment transaction.
4. Teacher views final salary details.

Dependencies:
- Attendance + Leave + Advances -> Salary -> Reporting.

### F) Tuition Invoice and Payment Workflow
1. Invoice generated with tuition + tax components.
2. Parent/student receives payment reminder/notification.
3. Online payment through MCB merchant flow.
4. Payment recorded and reflected in history/status.

Dependencies:
- Student billing -> Payment gateway -> Financial records -> Notifications.

---

## Financial Rules

Salary-side rules:
- Salary is calculated from:
  - Base salary
  - Late arrival deductions
  - Leave deductions
  - Approved advance salary adjustments
- Late rule: if teacher arrives **more than 15 minutes late**, status marked late and deduction calculated **per minute** (rate unspecified).
- Teachers exceeding annual leave allowance incur salary deduction.
- Admin approves salary payouts and records transactions.

Tuition-side rules:
- Invoice must include:
  - Tuition fee amount
  - FBR tax
  - Applicable government taxes
  - Total payable amount
- Payment methods: debit card and credit card.
- Payments settle directly to academy bank account via MCB merchant integration.

---

## Academic Rules

### Attendance Rules
- Teacher attendance logs login/logout timestamps.
- Late attendance threshold is 15 minutes.
- Student attendance must be marked per scheduled class as present/absent.
- Student absence can trigger slot availability and potential reassignment.

### Lesson Summary Rules
- Submission required after each class.
- Required fields:
  - Student name
  - Lesson topic
  - Surah/lesson practiced
  - Memorization progress
  - Student performance notes
  - Homework assigned
- Immutable after submission (no edit allowed).

### Homework/Task Rules
- Teachers can assign homework, revision, memorization, and practice tasks.
- Students access tasks in their dashboard.
- Task outcomes are expected to be reflected in reporting.

---

## Notifications

Trigger types defined:
- Class assignments
- Teacher absence
- Student absence
- Payment reminders
- Invoice generation
- Lesson reschedule requests
- Teacher availability alerts
- Violation alerts

Delivery channels:
- In-portal notifications
- Email
- WhatsApp

Architecture implication:
- Needs event-driven notification orchestration with channel preferences/fallback and delivery status tracking.

---

## Reports

Required report families:

1. **Teacher Reports**
   - Monthly attendance
   - Teaching performance
   - Lesson activity

2. **Student Reports**
   - Attendance
   - Academic progress
   - Task completion

3. **Financial Reports**
   - Tuition payments
   - Salary payments
   - Outstanding invoices

Export formats:
- PDF
- Excel

---

## Security Requirements

Mandatory controls from SRS:
- Role-based access control (RBAC).
- Secure authentication and session handling.
- Encrypted password storage.
- Secure payment gateway integration.
- Activity/audit logs.
- Regular backups.

Operational security implications:
- Enforce least privilege with explicit permission matrix.
- Protect financial and personal data by role and endpoint policy.
- Ensure non-repudiation for immutable and approval-bound actions (e.g., lesson summary, leave/salary approvals).

---

## UI and Table Requirements

### Core UI stack
- Laravel Blade frontend.
- jQuery for form validation/interaction.
- DataTables for listing pages.

### DataTables requirements
- Real-time search.
- Pagination.
- Column sorting.
- Responsive design.
- Instant filtering (AJAX or client-side).

### Search criteria
- Name
- Unique ID
- Email
- Status (Active/Inactive)

Primary pages impacted:
- Teacher management
- Student management
- Potentially user listings and operational monitoring pages.

---

## Suggested MVP Scope

### Core MVP (recommended for first release)
1. Authentication + RBAC.
2. User onboarding for Teacher/Student/Supervisor (HR/Admin flows).
3. Slot-based scheduling (Supervisor).
4. Teacher attendance + student attendance.
5. Mandatory immutable lesson summaries.
6. Basic task assignment and student task view.
7. Leave request and approval chain (Teacher -> Supervisor/HR -> Admin).
8. Basic salary calculation (base + late + leave + advance adjustment) with admin approval.
9. Basic tuition invoice generation + manual/initial payment status tracking.
10. In-portal notifications.
11. Foundational reports (attendance, lesson activity, payment status).

### Advanced/Post-MVP
1. Full MCB payment gateway integration (live settlement handling).
2. WhatsApp channel integration and delivery analytics.
3. Deep financial reporting and reconciliation dashboards.
4. Advanced violation engine and policy automation.
5. Parent self-service payment UX enhancements.
6. Sophisticated schedule optimization and auto-reassignment suggestions.
7. Rich audit/compliance reporting and backup recovery tooling.

---

## Missing Clarifications / Assumptions

### Open questions (to clarify before implementation)
1. **Role overlap**: Accountant role is listed but portal pages/permissions are not defined.
2. **Leave approval path ambiguity**: “Supervisor or HR” then “Supervisor forwards to Admin” is unclear when HR receives first.
3. **Leave type policy**: Are all 12 annual leaves paid? Any distinction (sick/casual/emergency)?
4. **Leave cycle**: Calendar year vs contract year vs rolling 12 months.
5. **Late deduction formula**: Per-minute deduction rate and cap are not specified.
6. **Grace logic**: Is exactly 15 minutes late considered on-time or late? SRS says “more than 15”.
7. **Attendance source**: Is teacher “login/logout” the official attendance marker, or separate attendance action required?
8. **Class start constraints**: Can a teacher start class without prior attendance sign-in?
9. **Student absence compensation**: If student absent, does teacher still receive payment for that slot?
10. **Reassignment window**: Is reassignment allowed only before slot start, during slot, or post-marking absent?
11. **Lesson summary timing**: Deadline for submission (immediate, same day, configurable)?
12. **Immutable summary exceptions**: Should admin/supervisor have correction override with audit log?
13. **Progress metrics**: Definition of memorization progress scale and performance rubric.
14. **Task completion model**: How are tasks marked complete and by whom (student/teacher)?
15. **Reschedule approvals**: Can supervisors reject; is parent confirmation needed for minors?
16. **Fee model**: Monthly flat tuition vs per-class billing; proration rules.
17. **Tax configuration**: Source and change management for FBR/government tax rates.
18. **Invoice cadence**: Auto-generation schedule and due-date rules.
19. **Payment failures/refunds**: Required workflows and status model.
20. **Currency and rounding**: Rounding policy for taxes/deductions.
21. **Advance salary workflow**: Who can request (HR/teacher), who approves, deduction recovery schedule.
22. **Violation policy**: Violation categories, thresholds, escalation, expiry/closure rules.
23. **Notification preferences**: Per-user channel opt-in, timing, language template requirements.
24. **Parent-student mapping**: One parent to many students? Multiple guardians per student?
25. **Timezone policy**: Single academy timezone vs user-local timezone.

### Working assumptions (until clarified)
- Admin is final approval authority for financial and leave outcomes.
- Teacher and student profiles remain active/inactive (soft status lifecycle).
- All sensitive edits/actions require audit logging.
- Immutable lesson summaries are hard-locked after submit.
- Supervisor is operational owner of schedule integrity.

---

## Suggested Technical Architecture (Laravel 12 + Blade + jQuery + DataTables + MySQL)

### Architectural style
- Modular monolith with clear bounded contexts:
  - Identity & Access
  - Academic Operations
  - Scheduling & Attendance
  - HR & Leave
  - Finance (Salary/Billing/Payments)
  - Notifications
  - Reporting

### Backend foundations
- Laravel 12 for domain/application layers.
- Spatie Roles & Permissions for RBAC.
- Policy/Gate authorization per action.
- Service classes for business workflows (leave approvals, salary calculation, invoice lifecycle).
- Form Requests for validation.
- Database transactions for approval/payment/salary critical writes.
- Queue workers for notifications and heavy report generation.
- Scheduled commands for monthly invoice/salary cycles and reminders.

### Data and domain modeling (high-level)
- Core entities: Users, Roles, Teachers, Students, Parents, Supervisors.
- Operational entities: TimeSlots, ClassSchedules, ClassSessions, AttendanceRecords, LessonSummaries, Tasks, LeaveRequests, Violations.
- Finance entities: SalaryPeriods, SalaryAdjustments, SalaryPayments, Invoices, InvoiceItems, PaymentTransactions, TaxConfigurations, AdvanceSalaryRequests.
- Support entities: Notifications, NotificationDeliveries, AuditLogs, SystemSettings.

### UI implementation approach
- Blade-based role dashboards and portal pages.
- jQuery for form interactions and client-side validation enhancements.
- DataTables for searchable/sortable/paginated listings (server-side mode preferred for scalability).
- Reusable partials/components for approval cards, status badges, timeline/history, and table filters.

### Reporting approach
- Query layer optimized for aggregate reporting.
- Export pipeline:
  - Excel via dedicated export package.
  - PDF via template-to-pdf renderer.
- Async report generation for large datasets with download history.

### Security and reliability
- Encrypted credentials/secrets and secure reset tokens.
- Audit trail for sensitive operations (approvals, salary, invoice status changes).
- Backup strategy with periodic restore testing.
- Payment callback verification and idempotent transaction handling.
- Strict separation of financial data visibility by role policies.

### Integration strategy
- MCB payment integration via gateway abstraction interface (pluggable provider pattern).
- Multi-channel notification adapters (portal/email/WhatsApp) behind unified notification dispatcher.

---

## Dependency Map (Cross-Module)

- **Scheduling** depends on Teacher/Student availability and role permissions.
- **Attendance** depends on Scheduling (who is expected in which slot).
- **Lesson Summary** depends on class session completion context.
- **Leave** affects Attendance and Scheduling (replacement flow).
- **Salary** depends on Attendance + Leave + Advance requests + policy configuration.
- **Invoicing/Payments** depend on Student enrollment/billing policy and tax configuration.
- **Notifications** depend on events from all operational modules.
- **Reports** depend on normalized, audit-ready records from academic + finance modules.

