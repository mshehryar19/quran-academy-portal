# Quran Academy Online Portal – Project Overview

## Project Name
Quran Academy Online Portal

## System Purpose
Build a centralized web platform for daily Quran academy operations across academics, attendance, staffing, leave, salary, student billing, and communication, with strict role-based access and audit-friendly records.

## Business Overview
The portal connects Admin, HR, Supervisors, Teachers, Students, Parents, and (later) Accountant users in one system. It is designed to:
- run class operations reliably,
- improve transparency in attendance and salary deductions,
- track student academic progress and homework completion,
- manage monthly tuition invoices and payment visibility,
- enforce workflow approvals and policy communication.

## Teaching Model
- One class session = **1 teacher + 1 student + 30 minutes**.
- Teachers can teach multiple slots per day.
- Multiple classes may run simultaneously across different teacher-student pairs.

## User Groups
- Admin
- HR
- Supervisor
- Teacher
- Student
- Parent
- Accountant (planned; detailed scope pending)

## Main Portal Sections
- Authentication and role dashboards
- User/staff/student/parent management
- Class scheduling and replacement handling
- Attendance (including separate attendance page)
- Lesson summaries and homework/tasks
- Leave approvals and balances
- Salary and advance salary processing
- Monthly invoicing and payment tracking
- Notifications and violation/policy alerts
- Reports and exports

## Technology Stack
- Laravel 12 (backend/application architecture)
- Blade (server-rendered UI)
- jQuery (forms and page interactions)
- DataTables (search, filter, sort, pagination)
- MySQL (relational data storage)

## Implementation Philosophy
- **Modular**: feature domains separated for maintainability.
- **Phased**: dependencies built in practical order.
- **Role-secured**: permission-first route and action controls.
- **Audit-friendly**: immutable and approval-sensitive actions logged.

## MVP Summary
MVP covers operational essentials:
- auth + RBAC,
- core master data,
- scheduling,
- separate attendance flow + class attendance,
- immutable lesson summaries with admin override trail,
- leave workflow (supervisor review, admin final),
- salary core deductions and advance deduction baseline,
- monthly flat tuition invoice auto-generation baseline,
- parent visibility,
- core notifications and reports.

## Advanced Features Summary
Advanced/later phases include:
- live payment gateway processing and reconciliation,
- WhatsApp provider delivery workflows,
- accountant portal expansion,
- advanced analytics and reporting,
- refined tax/refund/failure policies.

## Postponed Finance/Integration Scope
Some finance and integration details are intentionally deferred to later phases (e.g., refund flow, payment failure workflow, tax source/update process, rounding policy, detailed accountant permissions, strict alternative advance approval chain).
