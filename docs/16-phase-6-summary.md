# Phase 6 Summary — Student Billing & Invoices (GBP/USD)

**Status:** Complete for Phase 6 scope (no live MCB gateway, no notification delivery, no refund workflow, no finance export module, no payroll/leave changes).

## What Was Implemented

### Student fee profiles (`student_fee_profiles`)

- Per-student **monthly flat** tuition amount with **currency** (strictly **GBP** or **USD**).
- **effective_from** / optional **effective_to**, **active** / **inactive** status, optional **notes**.
- **Resolver** (`StudentFeeProfileResolver`) picks the current profile for a calendar month: active row overlapping month start/end, latest `effective_from` if multiple.
- Separate from **PKR** employee payroll (no shared tables).

### Invoices / vouchers (`invoices`)

- Linked to **student** and optional **student_fee_profile_id** (historical reference).
- **billing_year** / **billing_month** billing period.
- Monetary columns (same **currency** as fee at generation): **tuition_amount**, **tax_amount** (default 0), **tax_detail** (nullable JSON for future multi-line tax), **total_amount** (= tuition + tax via BC math in code), **amount_paid** (cached sum driver for statuses).
- **due_date**: end of billing month **+ 7 days** (simple documented default).
- **status**: `unpaid`, `partially_paid`, `paid`, `overdue`, `cancelled`.
- **Gateway readiness**: `gateway_reference`, `billing_source` (`internal` by default).
- **Void**: `void_reason`, `voided_at`; blocked if any **completed** payments exist.

### Payments (`invoice_payments`)

- **amount**, **currency** (must match invoice — enforced in `InvoiceTotalsService::assertPaymentFits`).
- **paid_on**, **method** (manual, bank_transfer, card, cash, online_pending), **reference**, **gateway_transaction_id**, **channel** (default `manual`), **payment_status** (`completed` default; `pending`/`failed` reserved for future gateway).
- **recorded_by_user_id** for audit.
- **Partial payments** supported; total paid cannot exceed invoice **total_amount** (validation).

### Outstanding balance & statuses

- **Balance** = `max(0, total_amount - amount_paid)` via `Invoice::balanceOutstanding()` / `balanceFormatted()`.
- `InvoiceTotalsService::refresh()` recomputes **amount_paid** from completed payments, sets **unpaid** / **partially_paid** / **paid**, then sets **overdue** if not fully paid, **due_date** in the past, and balance &gt; 0.

### Invoice numbering

- **Format:** `INV-{YEAR}-{NNNN}` (e.g. `INV-2026-0001`).
- **Per-calendar-year** sequence rows in `identifier_sequences` named `invoice_YYYY`, allocated in `InvoiceNumberService` with **row lock** (same pattern as public IDs).

### Generation

- **Admin UI:** `GET/POST admin/billing/invoices/generate` — pick year/month; optionally restrict to selected students; unchecked = all students with an overlapping **active** fee profile.
- **CLI:** `php artisan billing:generate-month {year} {month} [--student=]` (no user id for `generated_by_user_id`).
- **Duplicate guard:** no second **non-cancelled** invoice for the same **student + billing month**.

### Tax-ready design

- **tuition_amount** and **tax_amount** stored separately; **tax_detail** JSON optional.
- Generation sets **tax = 0**; staff can **PATCH** tax on an invoice (admin); **total** recalculated; blocked if total would fall below **amount_paid**.

### Student / parent visibility

- Permission **`student_billing.view`** (Student + Parent + included in Admin’s full permission set).
- **Student:** `/my-billing` — own invoices (non-cancelled listed).
- **Parent:** `/parent/billing` — linked students; per-student invoice list and invoice detail.
- Authorization: **`InvoicePolicy::view`** — student must own `invoice.student_id`; parent must link to that student via `parent_student`.

### Access control summary

| Role | Fee profiles | Invoices / generate / void / tax | Record payment | Portal billing view |
|------|--------------|----------------------------------|----------------|---------------------|
| Admin | yes (`invoice.manage`) | yes | yes (`payment.manage`) | optional (`student_billing.view`) |
| Accountant | yes | yes | yes | — |
| HR / Supervisor / Teacher | no | no | no | no |
| Student | no | no | no | own |
| Parent | no | no | no | linked children |

Routes: **`admin/billing/*`** use **`auth` + `role:Admin|Accountant`** (not the general HR/Supervisor admin group).

## Migrations

- `2026_03_28_100000_create_student_fee_profiles_table`
- `2026_03_28_100001_create_invoices_table`
- `2026_03_28_100002_create_invoice_payments_table`

## Key new / updated files

- **Models:** `StudentFeeProfile`, `Invoice`, `InvoicePayment`; `Student` relations.
- **Services:** `InvoiceNumberService`, `StudentFeeProfileResolver`, `InvoiceGenerationService`, `InvoiceTotalsService`.
- **Policies:** `StudentFeeProfilePolicy`, `InvoicePolicy`, `InvoicePaymentPolicy`.
- **Requests:** `App\Http\Requests\Billing\*`.
- **Controllers:** `App\Http\Controllers\Admin\Billing\*`, `App\Http\Controllers\Portal\*Billing*`.
- **Command:** `App\Console\Commands\GenerateMonthlyInvoicesCommand`.
- **Routes:** `routes/billing.php`; `routes/web.php` include.
- **Views:** `resources/views/admin/billing/**`, `resources/views/portal/**billing**`.
- **Seeder:** `student_billing.view` permission; Student & Parent assignments.

## Activity logging

- Fee profile create/update/delete; invoice generated; tax updated; invoice voided; payment recorded.

## Assumptions

- **Due date** rule (month-end + 7 days) is interim; refine when policy is finalized.
- **Tax** entry is manual/admin for now; automation can replace `tax_amount` / `tax_detail` population later.
- **Online payments:** fields exist; MCB and webhooks are a later phase.

## Next recommended step

- Phase 7+ **notifications** and **exports**; gateway callbacks updating `invoice_payments.payment_status` and `gateway_transaction_id`; refund/credit-note flow when rules are fixed.
