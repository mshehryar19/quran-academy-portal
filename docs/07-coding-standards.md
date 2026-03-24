# Coding Standards and Implementation Conventions

## Scope
These standards apply to all future implementation work in this Laravel 12 project.

## Core Architecture Guidance
- Use modular feature organization while retaining Laravel conventions.
- Keep controllers thin; move business workflows into service/action classes.
- Enforce authorization in policies/middleware, not only UI.
- Favor explicit, auditable state transitions for approvals and immutable records.

## Laravel 12 Project Structure Recommendations
- `app/Http/Controllers/{Role|Module}` for route handling.
- `app/Http/Requests/{Module}` for validation logic.
- `app/Services/{Module}` or `app/Actions/{Module}` for business rules.
- `app/Models` for Eloquent entities with clear relationships/scopes.
- `app/Policies` for per-model authorization.
- `app/Enums` (or constants) for statuses and workflow states where practical.

## Blade Folder Organization
- Use role- and module-based structure under `resources/views`:
  - `layouts/` for base templates.
  - `components/` or partials for reusable UI blocks.
  - `admin/`, `hr/`, `supervisor/`, `teacher/`, `student/`, `parent/`.
  - module subfolders such as `attendance/`, `scheduling/`, `leave/`, `salary/`, `billing/`.

## Route Grouping Conventions
- Group routes by role prefix and middleware:
  - `/admin/*`, `/hr/*`, `/supervisor/*`, `/teacher/*`, `/student/*`, `/parent/*`.
- Keep attendance terminal page in a dedicated protected route group with separate access policy.
- Use named routes consistently (e.g., `admin.leave.index`, `teacher.lesson-summaries.store`).

## Controller Naming Conventions
- Use explicit controller names by module and responsibility:
  - `LeaveRequestController`, `LeaveApprovalController`, `SalaryRunController`, `AttendanceTerminalController`.
- Avoid generic `CommonController` patterns for policy-heavy modules.

## Request Validation Approach
- Use Form Request classes for all create/update endpoints.
- Include role-aware validation constraints where needed.
- Validate uploaded files (medical attachments) by size/type/security restrictions.

## Service / Action Class Recommendations
- Put workflow logic into dedicated classes:
  - leave lifecycle and finalization,
  - attendance/late computation,
  - salary calculations,
  - invoice generation,
  - notification dispatch.
- Keep service methods deterministic and testable.

## Model Naming Conventions
- Use singular PascalCase model names mapped to plural snake_case tables.
- Add relationship methods with clear intent (`teacher()`, `student()`, `reviews()`).
- Use scopes for common filters (active, pending, approved, month range).

## Migration Naming Conventions
- Use descriptive migration names tied to domain intent.
- Keep schema changes small and reversible.
- Add indexes for IDs, foreign keys, status fields, and report-heavy timestamps.

## jQuery Usage Boundaries
- Use jQuery for light interactivity, form behavior, and AJAX-based table/filter interactions.
- Do not implement core business decisions in frontend scripts.
- Keep server as source of truth for workflow rules.

## DataTables Usage Recommendations
- Use DataTables for list-heavy pages (teachers, students, leaves, invoices, reports).
- Prefer server-side mode when datasets are expected to grow.
- Standardize search fields: name, ID, email, status.

## Reusable Layout/Partial/Component Approach
- Centralize headers, sidebars, alerts, table wrappers, modal shells.
- Reuse status badges and timeline/history partials for approvals and audit views.

## Modal/Form Handling Guidance
- Use modals for quick review actions; use full-page forms for critical operations.
- Require confirmation steps for irreversible or high-risk actions.
- Show comment/reason fields where workflow requires reviewer input.

## Audit Logging Guidance
- Log all sensitive operations:
  - leave reviews/final decisions,
  - salary approvals/adjustments,
  - lesson summary overrides,
  - invoice/payment status changes,
  - violation notices.
- Capture actor, timestamp, previous state, new state, and reason/comment where applicable.

## Immutable-Record Handling Guidance
- Treat lesson summaries as immutable after submission.
- Only Admin override path is allowed.
- Preserve original data and create a linked audit/override record.

## Financial/Security-Sensitive Cautions
- Keep payment and salary endpoints strongly policy-guarded.
- Validate and sanitize all financial input fields.
- Avoid hidden trust in client-side values for amounts/deductions.
- Use transactions for multi-step financial writes.

## Timezone Handling Guidance
- Store core operational timestamps in server-consistent format (UTC recommended).
- Render user-facing times in each user’s local timezone.
- Ensure reports clearly specify timezone context.

## File Upload Guidance (Medical Attachments)
- Restrict file types and maximum size.
- Store uploads in protected storage with controlled access.
- Keep file metadata linked to leave request and audit access.

## Business Logic Placement Rule
- Do not place heavy business logic directly in Blade templates or route closures.
- Keep logic in services/actions + model policies + validated requests.

## Delivery Strategy Notes
- Prefer admin-first implementation for governance modules (RBAC, approvals, audit).
- Use modular feature folders where useful for long-term maintainability.
