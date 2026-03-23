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

### Finalized Clarifications (Now Confirmed System Rules)

1. **Accountant role (planned, not finalized)**
   - Accountant remains a valid role in the platform roadmap.
   - Current intended scope includes student payment records, invoice/payment monitoring, and financial reports.
   - Salary transaction access is potential future scope (view/record only if later approved).
   - Detailed pages and final permission matrix are deferred until dedicated requirements are completed.

2. **Leave approval workflow (final authority: Admin)**
   - Teacher/employee submits leave request.
   - Supervisor reviews first and can approve/reject with optional comment.
   - After supervisor action, Admin reviews and gives final approve/reject decision with optional comment.
   - Final status always follows Admin decision:
     - Supervisor rejected + Admin approved = **Approved**.
     - Supervisor approved + Admin rejected = **Rejected**.
   - HR does not perform final approval.
   - HR must have a dedicated leave-monitoring page for full request visibility, statuses, counts, history, and records.

3. **Leave policy and leave categories**
   - Each employee is entitled to **12 annual leaves**.
   - These regular annual leaves are paid by default.
   - Leave records must explicitly support a payment status: **Paid** or **Unpaid**.
   - For unpaid leave, leave type must be captured.
   - Supported leave types: **Medical**, **Casual**, **Emergency**, **Unpaid**.
   - Leave requests must support medical attachment upload where required.

4. **Leave cycle basis**
   - Leave cycle is based on **employee appointment date**, not calendar year.
   - Leave balance renews automatically after each completed one-year service cycle from joining date.

5. **Late deduction formula**
   - Late salary deduction is calculated using employee per-minute salary value derived from base salary.
   - Deduction applies to all counted late minutes.
   - No deduction cap currently applies.

6. **Grace period and deduction application**
   - Up to and including **15 minutes late** is treated as no-deduction grace.
   - At **16 minutes late or more**, deduction applies to the **full late duration** (not only minutes above 15).

7. **Attendance mechanism**
   - Attendance is captured through a **separate attendance page**.
   - Attendance uses portal-assigned ID with **digits-only entry** for attendance input convenience.
   - This attendance page is Admin-controlled and not part of standard employee portal navigation.

8. **Attendance non-blocking rule**
   - Teachers can start/manage classes even if attendance sign-in/sign-out was missed.
   - Reporting must explicitly flag missing attendance events (e.g., did not login, did not logout).

9. **Student absence compensation**
   - Teacher is paid fully for a class slot even when student is absent.
   - No teacher salary deduction is applied due to student absence.

10. **Reassignment control window**
   - When student is marked absent, teacher can mark self available for that slot.
   - Supervisor can reassign only to a compatible free/empty slot with no teacher assigned.
   - Reassignment must not occur mid-way in an already running class/slot.

11. **Lesson summary timing**
   - Expected submission is immediately after class.
   - Allowed grace window: same day or next day submission.

12. **Lesson summary override control**
   - Lesson summaries are immutable by default.
   - Only Admin can override/correct a submitted summary.
   - Every override must be captured in audit trail logs.

13. **Task completion ownership**
   - Task/homework completion status is teacher-marked as completed or not completed.
   - Parent portal must expose task/homework completion status.

14. **Reschedule request decisioning**
   - Student can submit a reschedule request.
   - Supervisor can approve or reject.
   - Rejection should support supervisor comment/reason.
   - Parent confirmation is not required in current scope.

15. **Fee model**
   - Tuition is **monthly flat fee**, not per-class billing.

16. **Tax and payment scope (current planning phase)**
   - Immediate operational focus is international fee payments.
   - Tax operational detail remains partially deferred pending final tax process definition.

17. **Invoice cadence baseline**
   - Fee vouchers/invoices must be auto-generated.
   - Exact generation day, due date, and reminder schedule remain configurable/open.

18. **Currency policy**
   - Default fee currency: **GBP**.
   - System must also support **USD** as selectable fee currency.
   - Currency value must be stored with each fee/billing record.

19. **Advance salary baseline rule**
   - Any employee can request advance salary.
   - Approved advance amount is deducted from the next salary cycle.

20. **Violation and policy alerts model**
   - Admin can issue violation notices, rule notices, and policy/disciplinary alerts.
   - Category system is flexible and not restricted to a fixed master list at this stage.
   - Detailed messages can be sent by email; concise alert should also appear in portal notifications.

21. **Notification channel selection**
   - Admin can choose delivery channel(s) per notification:
     - WhatsApp
     - Email
     - Portal notification

22. **Parent-student relationship model**
   - One parent can be linked to multiple students.

23. **Timezone policy**
   - System should display times based on user local timezone where applicable.

### Still Open / To Be Decided Later

1. Detailed Accountant portal pages and final permission scope.
2. Memorization progress scale and performance rubric definition.
3. Payment failure workflow.
4. Refund workflow.
5. Tax configuration source and update process.
6. Currency rounding policy.
7. Exact invoice generation date and due-date rules.
8. Exact advance salary approval chain if stricter workflow is required.

### Updated Working Assumptions

- Admin remains the final authority for leave outcome and key financial approvals.
- Teacher and student lifecycle supports active/inactive status management.
- Sensitive edits and approval actions are audit logged.
- Supervisor remains operational owner of schedule execution and rescheduling control.

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

