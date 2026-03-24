# Confirmed Business Rules

## Attendance Rules
- Attendance sign-in/sign-out is handled through a **separate attendance page**.
- The attendance page is **admin-controlled** and outside normal employee portal navigation.
- Attendance entry uses **digits-only ID input** for quick operational use.
- Attendance captures login and logout events.
- Teachers can still use their normal portal and manage classes even if attendance was not marked.
- Reports must explicitly show attendance gaps such as:
  - did not login
  - did not logout

## Late Arrival Rules
- Grace period is **up to and including 15 minutes**.
- Late deduction starts at the **16th minute**.
- Once late deduction applies, it applies to the **full late duration** (not only minutes beyond 15).
- There is currently **no deduction cap**.
- Deduction uses per-minute salary value derived from base salary.

## Student Absence Rules
- Teacher marks student absent per class attendance flow.
- Teacher can mark self available for the affected slot.
- Teacher still receives full salary/payment for that student-absent slot.
- Supervisor reassignment is limited to:
  - compatible slot where teacher is free/empty, and
  - slot with no teacher assigned.
- Reassignment must not occur mid-way in an already running slot/class.

## Lesson Summary Rules
- Teacher should submit lesson summary immediately after class.
- If missed, submission is allowed on the same day or next day.
- Lesson summary is immutable after submission.
- Only Admin can override/correct a submitted summary.
- Every override must be captured in audit logs.

## Leave Rules
- Annual entitlement: **12 leaves per employee**.
- Leave cycle starts from appointment date (not calendar year).
- Leave balance auto-renews after each completed one-year cycle from appointment date.
- Leave record must support **Paid** or **Unpaid** status.
- Supported leave types:
  - Medical
  - Casual
  - Emergency
  - Unpaid
- Leave request supports medical attachment upload where required.
- Workflow:
  1. Employee submits leave.
  2. Supervisor first review (approve/reject with optional comment).
  3. Admin final decision (approve/reject with optional comment).
- Admin final decision determines final status in all cases.
- HR must be able to view leave records, statuses, counts, and history but does not final-approve.

## Task / Homework Rules
- Teacher assigns homework/tasks.
- Teacher marks completion status (completed/not completed).
- Parents can monitor homework/task completion status for linked students.

## Salary / Advance Rules
- Teacher salary details page is view-only for teachers.
- Advance salary can be requested by any employee.
- Approved advance salary is deducted from the next salary period.
- Late deduction and leave impacts must feed salary calculations as confirmed.
- **Open note**: stricter or multi-step advance approval chain remains future clarification.

## Fee / Invoice Rules
- Tuition model is monthly flat fee (not per-class billing).
- Current operational payment scope focuses on international fees.
- Invoice/voucher generation must be automated.
- Default currency is GBP.
- USD must be supported as selectable currency.
- Currency must be stored with each fee/invoice record.

## Notification Rules
- Admin can choose delivery channel(s) per notification:
  - Portal
  - Email
  - WhatsApp
- Violation/policy notices can be sent in long form by email and short form in portal notification.

## Parent-Student Relationship Rules
- One parent can be linked to multiple students.

## Timezone Rules
- System should support user local timezone display where applicable.

## Open Business Items Still Not Finalized
- Final accountant portal pages and permission granularity.
- Memorization progress scale/performance rubric.
- Payment failure workflow.
- Refund workflow.
- Tax configuration source/update process.
- Currency rounding policy.
- Exact invoice generation date and due-date rules.
- Strict advance salary approval chain (if introduced later).
