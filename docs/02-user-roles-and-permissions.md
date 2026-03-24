# User Roles and Permissions

## Role Access Principles
- Enforce least privilege with RBAC and policy checks.
- Keep financial access separated from operational/academic access.
- Apply explicit approval authority rules (especially leave and salary).

## Role Matrix

### 1) Admin
- **Role purpose**: Final authority and full platform governance.
- **Main portal pages**: Dashboard, User Management, Leave Final Decisions, Salary Management, Invoice/Payment Management, Reports, Notifications, System Settings.
- **Allowed actions**:
  - Full CRUD and status controls where policy permits.
  - Final leave approval/rejection.
  - Salary approval and transaction recording.
  - Notification channel selection (portal/email/WhatsApp).
  - Lesson summary override with audit logging.
- **Restricted actions**: None by business scope (subject to security controls).
- **Financial boundary**: Full finance visibility and approval authority.
- **Academic boundary**: Full oversight; can review/override where policy allows.
- **Approval authority**: Final leave authority; salary approval authority.

### 2) HR
- **Role purpose**: Staff/student onboarding and record administration.
- **Main portal pages**: Dashboard, Add Teacher, Add Supervisor, Add Student, Leave Monitoring, Advance Salary Requests (operational support view).
- **Allowed actions**:
  - Manage onboarding/master records.
  - View leave records/status/counts/history.
- **Restricted actions**:
  - No final leave approval.
  - No direct salary/payment detail governance.
- **Financial boundary**: Cannot access detailed salary/payment records.
- **Academic boundary**: No class execution ownership.
- **Approval authority**: None for final leave/salary decisions.

### 3) Supervisor
- **Role purpose**: Operational owner of scheduling and class continuity.
- **Main portal pages**: Dashboard, Class Scheduling, Teacher Availability, Teacher Monitoring, Replacement, Reschedule Requests, Leave First Review.
- **Allowed actions**:
  - Assign/modify schedules.
  - Handle teacher replacement and slot reassignment per rules.
  - First-stage leave review (approve/reject + optional comments).
  - Review student reschedule requests (approve/reject + comments).
- **Restricted actions**:
  - No financial data access.
  - No final leave authority.
- **Financial boundary**: No salary/payment visibility.
- **Academic boundary**: Full operations visibility; not a final finance approver.
- **Approval authority**: First-stage leave review only.

### 4) Teacher
- **Role purpose**: Deliver classes and maintain academic records.
- **Main portal pages**: Dashboard, My Schedule, Student Attendance, Lesson Summary, Task Assignment, Leave Requests, Salary Details (view-only), Violations, Profile.
- **Allowed actions**:
  - Mark student attendance.
  - Submit lesson summaries.
  - Assign tasks and mark task completion status.
  - Submit leave requests.
  - View salary breakdown.
- **Restricted actions**:
  - No salary approval/payment controls.
  - No final leave decisions.
  - No financial administration.
- **Financial boundary**: Salary details view-only.
- **Academic boundary**: Own assigned classes/students and related records.
- **Approval authority**: None.

### 5) Student
- **Role purpose**: Access personal learning schedule and outcomes.
- **Main portal pages**: Dashboard, Schedule, Lesson Summaries, Homework/Tasks, Progress, Payment Status, Reschedule Request, Profile.
- **Allowed actions**:
  - View academic records and tasks.
  - Submit reschedule requests.
- **Restricted actions**:
  - No scheduling control authority.
  - No financial administration.
- **Financial boundary**: View personal fee/payment status only.
- **Academic boundary**: Own records only.
- **Approval authority**: None.

### 6) Parent
- **Role purpose**: Monitor linked child/children academics and finance.
- **Main portal pages**: Dashboard, Lesson Summaries, Progress Reports, Attendance Reports, Payment History, Invoices.
- **Allowed actions**:
  - Monitor attendance, progress, and homework completion.
  - View invoices/payment history and pay tuition (when payment flow enabled).
- **Restricted actions**:
  - No class scheduling/teacher management controls.
  - No salary/HR controls.
- **Financial boundary**: Limited to linked students' billing/payment data.
- **Academic boundary**: Linked students only.
- **Approval authority**: None.

### 7) Accountant (Planned/Future)
- **Role purpose**: Finance-focused operational support (future finalized scope).
- **Main portal pages**: To be finalized.
- **Allowed actions (planned)**:
  - Payment record monitoring.
  - Invoice/payment reporting.
  - Possible salary transaction record support (if approved later).
- **Restricted actions (current)**:
  - Detailed permissions not finalized.
- **Financial boundary**: Planned role in finance, final boundaries pending.
- **Academic boundary**: No academic ownership expected.
- **Approval authority**: Not finalized.

## Mandatory Role Rules
- Admin is final authority for leave approval.
- HR can view leave records/counts/history but cannot final-approve leave.
- Supervisors manage scheduling and first-stage leave review.
- Teachers handle lesson summaries, student attendance marking, tasks, and leave requests.
- Parents monitor progress, homework completion, payment history, and invoices.
- Accountant remains planned until requirements are finalized.

## Role Overlap Notes
- Admin and Supervisor both interact with leave, but only Admin finalizes.
- HR and Supervisor both view leave context, but with different operational responsibilities.
- Teacher and Supervisor both influence schedule continuity (teacher availability vs supervisor assignment control).

## Future Permission Notes
- Accountant route groups and fine-grained finance permissions remain open.
- Possible stricter finance segregation by transaction type may be introduced later.

## Recommended Laravel RBAC Grouping
- `admin.*`: full governance routes.
- `hr.*`: onboarding and leave-monitoring routes.
- `supervisor.*`: scheduling, replacement, first-review workflows.
- `teacher.*`: class delivery and academic records.
- `student.*`: self-service learning views and requests.
- `parent.*`: linked-student monitoring and billing view/pay actions.
- `accountant.*` (future): finance monitoring/reporting routes when finalized.
