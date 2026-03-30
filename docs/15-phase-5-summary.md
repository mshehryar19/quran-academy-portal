# Phase 5 Summary — Leave & PKR Payroll Foundation

**Status:** Complete for Phase 5 scope (no student billing, invoices, gateway, notification engine, finance exports).

## What Was Implemented

### Leave requests (`leave_requests`)

- Fields: `leave_type` (medical, casual, emergency, unpaid), `is_paid`, `start_date`, `end_date`, inclusive `total_days`, `reason`, optional `attachment_path` (private disk).
- **Medical** leave requires an attachment (PDF/JPEG/PNG, max 5MB) on create.
- **Unpaid** leave type enforces `is_paid = false` in validation.
- Workflow columns: `supervisor_decision` / comment / user / timestamp; `admin_decision` / comment / user / timestamp.
- Employee **history** on `my-leave`; attachment download is authorized and audit-logged.

### Final leave status logic

- **Admin decision is the only final outcome** once set.
- **Supervisor must decide before admin** (enforced by `LeaveRequestPolicy::adminDecide`: `awaitingAdmin()`).
- Interpretation of business examples:
  - Supervisor approve + admin approve → **approved**
  - Supervisor approve + admin reject → **rejected**
  - Supervisor reject + admin approve → **approved** (admin overrides)
- Pending states: awaiting supervisor (`supervisor_decision` null); awaiting admin (`supervisor_decision` set, `admin_decision` null).

### Leave balance

- **12 paid days** per annual cycle (`LeaveBalanceService::ANNUAL_PAID_ENTITLEMENT_DAYS`).
- **Cycle anchor:** `teachers.date_of_appointment` when a `Teacher` row exists for the user; otherwise **`users.created_at`** (documented fallback for non-teacher staff).
- **Cycle boundaries:** advance anchor year-by-year until the cycle containing “today”; `currentCycleStart` / `currentCycleEnd`.
- **Usage:** sum `total_days` for **admin-approved**, **paid** requests whose **`start_date` falls inside** the current cycle (entire request counts toward the cycle of `start_date` — no split across cycle boundaries in this phase).
- **Day counting:** inclusive calendar days from start through end (**weekends and undifferentiated holidays included**; no holiday calendar in this phase).
- New **paid** requests are blocked if requested days exceed **remaining** paid balance.

### HR monitoring

- Route group: `hr/leaves`, `role:HR` + permission `leave.monitor`.
- Filters: employee, final status (open / approved / rejected), paid flag.
- Summary tiles: total, awaiting final, approved, rejected.
- **HR is not an approver**; views are read-only oversight.

### Employee salary foundation (PKR only)

- **`employee_salary_profiles`:** `user_id` (unique), `base_salary_pkr`, `effective_from`, `notes`.
- **`monthly_salary_records`:** per user + `period_year` + `period_month` (unique), `base_salary_pkr`, `total_late_minutes`, `late_deduction_pkr`, `leave_deduction_pkr`, `unpaid_leave_days_in_period`, `advance_deduction_pkr`, `other_adjustment_pkr`, `final_payable_pkr`, `status` (`draft` | `finalized`), `calculation_notes`.
- **Admin** creates/edits profiles and **recomputes** drafts from operational data; **finalizes** a month (then employees with `salary.view` see that period).

### Late deduction readiness

- **Minutes:** summed from `employee_attendance_events` where `event_type = login` and `late_minutes` is set, for the teacher linked to the user (`Teacher.user_id`), in the payroll calendar month.
- **PKR per late minute** = `base_salary_pkr ÷ 10,560` (see formula assumptions below).
- **Late deduction** = `total_late_minutes ×` that rate (BC math then rounded display to 2 decimals).
- Grace **0–15 minutes** should already be reflected in stored `late_minutes` from Phase 4 attendance (null/zero = on time); payroll does not re-apply grace.

### Unpaid leave salary impact

- **Admin-approved** leaves with **`is_paid = false`** intersected with the payroll month; **inclusive** day count per overlap.
- **Daily rate** = `base_salary_pkr ÷ 22` (assumed working days per month).
- **Leave deduction** = `unpaid_leave_days_in_month × daily_rate`.
- **Paid leave** does not reduce salary in this phase (no deduction for paid days).

### Advance salary (`advance_salary_requests`)

- Any **Teacher, HR, Supervisor, or Admin** user may submit (`my-advances`); **Students/Parents** excluded by route middleware.
- **Admin-only** approve/reject (`admin/advances`).
- On **approve**, `deduction_period_year/month` set to **first day of the next calendar month** relative to decision time (simple, documented rule).
- **Payroll draft** sums **approved** advances scheduled for that **same** payroll month.
- On **finalize** of the monthly record, linked advances move to status **`deducted`** so they are not double-counted.

### Formula assumptions (refine later)

| Element | Assumption |
|--------|------------|
| Working minutes / month | **22 × 8 × 60 = 10,560** for late-minute rate from monthly base |
| Working days / month | **22** for unpaid-leave daily rate from monthly base |
| Next advance deduction month | **Next calendar month** from approval timestamp |

All amounts are **PKR**; no mixing with future student GBP/USD billing.

### Access control (summary)

| Area | Admin | HR | Supervisor | Teacher | Student/Parent |
|------|-------|-----|------------|---------|----------------|
| My leave | via permission | yes (`leave.request`) | yes | yes | no |
| Supervisor review | — | — | yes | — | — |
| Admin final leave | yes | — | — | — | — |
| HR leave monitor | — | yes | — | — | — |
| Salary profile / monthly / finalize | yes | no | no | no | no |
| My salary (finalized only) | if `salary.view` | **no** (`salary.view` not granted) | no | yes | no |
| Advance request | yes | yes | yes | yes | no |
| Advance admin decision | yes | — | — | — | — |

Spatie: added permission **`leave.monitor`**; **HR** gets `leave.request` + `leave.monitor` (apply seeder or sync).

## Migrations

- `2026_03_27_100000_create_leave_requests_table`
- `2026_03_27_100001_create_employee_salary_profiles_table`
- `2026_03_27_100002_create_advance_salary_requests_table` (index name `adv_salary_sched_idx`)
- `2026_03_27_100003_create_monthly_salary_records_table`

## Key New Files

- **Models:** `LeaveRequest`, `EmployeeSalaryProfile`, `AdvanceSalaryRequest`, `MonthlySalaryRecord`
- **Services:** `LeaveBalanceService`, `PayrollComputationService`
- **Policies:** `LeaveRequestPolicy`, `EmployeeSalaryProfilePolicy`, `MonthlySalaryRecordPolicy`, `AdvanceSalaryRequestPolicy`
- **Requests:** `Leave/*`, `Payroll/*`
- **Controllers:** `Employee/*`, `Supervisor/SupervisorLeaveController`, `Admin/LeaveFinalController`, `Admin/SalaryProfileController`, `Admin/MonthlySalaryRecordController`, `Admin/AdvanceSalaryAdminController`, `Hr/LeaveMonitoringController`
- **Routes:** `routes/payroll.php` + `routes/admin.php` (admin payroll & leave final)
- **Views:** `employee/leaves/*`, `employee/salary/*`, `employee/advances/*`, `supervisor/leaves/*`, `admin/leaves/*`, `admin/payroll/*`, `hr/leaves/*`

## Attachment handling

- Stored on **`local`** disk under `leave_attachments/`; not public; downloaded via `employee.leaves.attachment` after authorization.

## Activity logging (high level)

- Leave: submitted, attachment downloaded, supervisor decision, admin final.
- Salary: profile created/updated; monthly recomputed/finalized.
- Advance: submitted; admin decision.

## Next Recommended Step

- **Phase 8+ student billing** (GBP/USD) — keep isolated from `*_pkr` payroll tables.
- Optional: holiday-aware leave counting, split leave across appointment cycles, HR-safe “summary” tiles without salary figures, multi-step advance approval, **other_adjustment** editing on draft payroll lines.
