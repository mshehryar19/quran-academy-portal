# Database Planning (Non-Code)

This document defines likely entities, relationships, and data-planning priorities without locking final migration design.

## Planning Principles
- Normalize core transactional data.
- Keep policy-sensitive workflows auditable.
- Separate operational events (attendance, approvals, status changes) from summary aggregates.
- Support phased evolution: MVP first, refinements later.

## Core Entities / Tables Likely Needed

### Identity and Access
- `users` – authentication identity (**core MVP**)
- `roles`, `permissions`, role mapping tables (Spatie) (**core MVP**)
- `user_profiles` / role-specific profile tables (**core MVP**)
- `user_settings` (including timezone preference where needed) (**core MVP**)

### People and Relationships
- `teachers` (**core MVP**)
- `students` (**core MVP**)
- `parents` (**core MVP**)
- `supervisors` (**core MVP**)
- `staff_profiles` (common employee profile layer; optional normalized approach) (**core MVP**)
- `parent_student` mapping (many students per parent support) (**core MVP**)
- `accountants` or role-scoped finance profile (**later phase**)

### Academic Scheduling
- `class_slots` (30-minute templates/windows) (**core MVP**)
- `class_schedules` (teacher-student-slot-date mapping) (**core MVP**)
- `schedule_change_logs` (**core MVP**)
- `reschedule_requests` (**core MVP**)
- `teacher_availability` (**core MVP**)

### Attendance
- `attendance_logs` / `attendance_events` (login/logout events from separate page) (**core MVP**)
- `attendance_days` (optional daily normalized summary) (**later phase**)
- `student_attendance` (per class present/absent) (**core MVP**)
- `attendance_exceptions` (did-not-login/did-not-logout flags, if separated) (**later phase**)

### Academic Records
- `lesson_summaries` (**core MVP**)
- `lesson_summary_overrides` (admin-only corrections + before/after trail) (**core MVP**)
- `homework_tasks` (**core MVP**)
- `homework_task_status` / completion logs (**core MVP**)
- `progress_metrics` / rubric tables (**future refinement**)

### Leave and HR
- `leave_requests` (**core MVP**)
- `leave_reviews` (supervisor/admin decisions/comments) (**core MVP**)
- `leave_balances` (appointment-cycle based) (**core MVP**)
- `leave_types` lookup (**core MVP**)
- `leave_attachments` (medical proof uploads) (**core MVP**)
- `leave_policies` (**later phase**)

### Salary and Finance Operations
- `salary_records` / `salary_periods` (**core MVP**)
- `salary_adjustments` (late/leave/advance components) (**core MVP**)
- `salary_payments` (**core MVP**)
- `advance_salary_requests` (**core MVP**)
- `advance_salary_approvals` (if stricter future chain is introduced) (**future refinement**)

### Billing and Payments
- `fee_plans` (monthly flat fee definitions) (**core MVP**)
- `fee_vouchers` / `invoices` (**core MVP**)
- `invoice_items` (**core MVP**)
- `fee_payments` (**core MVP**)
- `payment_attempts` / gateway transaction tables (**later phase**)
- `refunds` (**future refinement**)
- `tax_configurations` (**future refinement**)
- `currencies` / currency metadata (**core MVP**)

### Notifications and Compliance
- `notifications` (**core MVP**)
- `notification_channels` / delivery logs (**core MVP**)
- `violations` / `policy_notices` (**core MVP**)
- `activity_logs` / `audit_logs` (**core MVP**)

## Key Relationships Overview
- One `user` maps to one primary role and related profile.
- One `parent` links to many `students` via mapping table.
- One `class_schedule` links one teacher and one student for a slot/date.
- One `class_schedule` has one `student_attendance` record per occurrence.
- One class occurrence should have one `lesson_summary`; admin override creates linked override records.
- One employee has many leave requests; each request can have multiple review actions.
- Salary records aggregate components from attendance, leave, and advances.
- One student has many invoices; one invoice has many items/payments.
- Notifications link to recipients and channel-specific delivery status.

## Audit-Sensitive Tables
- `leave_reviews`
- `salary_adjustments` and `salary_payments`
- `lesson_summary_overrides`
- `invoice` status/payment status logs
- `violations` / `policy_notices`
- `activity_logs` / `audit_logs`

## Immutable / Audit-Critical Areas
- `lesson_summaries` (immutable after submission by default)
- Override history table must preserve original state and actor metadata.
- Approval decisions (leave, salary) should be append-only in review/log tables.

## Likely Lookup / Master Tables
- `leave_types`
- `currencies`
- `notification_channel_types`
- `attendance_event_types`
- `status_codes` (if centralized enum strategy is used)

## Multi-Role Design Notes
- Keep `users` as canonical identity.
- Use role-specific profile tables where fields diverge significantly (teacher/student/parent/staff).
- Keep authorization rule definitions in RBAC and policies, not hard-coded in table assumptions.
- Keep Accountant-related financial entities extensible but non-blocking until role scope is finalized.
