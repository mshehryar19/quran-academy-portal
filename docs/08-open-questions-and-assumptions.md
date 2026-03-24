# Open Questions and Assumptions Tracker

This document separates finalized decisions from unresolved items to avoid requirement drift.

## Confirmed Decisions

- **Leave workflow**: Supervisor reviews first; Admin gives final decision. Admin decision is authoritative in all conflict cases.
- **HR leave role**: HR can view leave requests, counts, statuses, and history; HR does not final-approve leave.
- **Leave entitlement**: 12 annual leaves per employee.
- **Leave cycle**: Based on appointment date; auto-renew after each completed one-year cycle.
- **Leave payment status**: Leave must support Paid/Unpaid marking.
- **Leave types**: Medical, Casual, Emergency, Unpaid.
- **Medical proof**: Leave requests support medical attachment uploads.
- **Late deduction logic**:
  - up to and including 15 minutes: no deduction;
  - 16+ minutes: full late duration deducted;
  - deduction derived from per-minute salary from base salary;
  - no deduction cap currently.
- **Attendance design**:
  - separate admin-controlled attendance page;
  - digits-only attendance ID usage;
  - teachers can still use portal even if attendance sign-in/out was missed;
  - reports must show did-not-login / did-not-logout.
- **Student absence compensation**: Teacher still receives full slot payment when student is absent.
- **Reassignment control**: Reassignment allowed only to valid free/empty non-mid-slot scenarios.
- **Lesson summary timing**: Expected immediately; allowed same day or next day if missed.
- **Lesson summary immutability**: Immutable after submission; Admin-only override; all overrides audit logged.
- **Task model**: Teacher assigns and marks task completion; parents can monitor completion status.
- **Reschedule requests**: Supervisor can approve/reject with reason/comment support.
- **Tuition model**: Monthly flat fee (not per class).
- **Invoice generation**: Invoices/vouchers are auto-generated.
- **Currency policy**: GBP default, USD selectable; currency stored with billing record.
- **Advance salary baseline**: Any employee can request; approved advance deducted from next salary.
- **Notification channels**: Admin can choose portal/email/WhatsApp per notification.
- **Violation/policy notices**: Long-form allowed by email with concise portal notice.
- **Parent-student mapping**: One parent can be linked to multiple students.
- **Timezone policy**: User local timezone support for display context.

## Still Open / Future Clarifications

1. Final Accountant portal pages and detailed permission scope.
2. Memorization progress scale and performance rubric design.
3. Payment failure workflow and status handling.
4. Refund workflow and governance rules.
5. Tax configuration source, ownership, and update process.
6. Currency rounding and precision policy.
7. Exact invoice generation date and due-date/reminder policy.
8. Strict advance salary approval chain if additional governance is required later.

## Working Assumptions (Until Open Items Are Finalized)

- Open items must not block MVP unless they are direct prerequisites.
- Unfinalized finance policy details should be configurable rather than hard-coded.
- Accountant role remains planned and deferred for permission expansion.
