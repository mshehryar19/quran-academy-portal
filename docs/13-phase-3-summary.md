# Phase 3 Summary — Scheduling Core

**Status:** Complete for Phase 3 scope (no attendance, lesson summaries, leave, finance, notifications, student-facing reschedule requests).

## What Was Implemented

### Class slots (`class_slots`)
- Reusable **30-minute** time windows (`start_time` / `end_time`, enforced in validation).
- Optional **label**, **status** (active/inactive), **notes**.
- **Unique** `(start_time, end_time)` to prevent duplicate slot definitions.
- CRUD-style UI: index (DataTables), create, edit, show; destroy **deletes** only if no schedules reference the slot; otherwise **deactivates** and keeps historical rows.

### Class schedules (`class_schedules`)
- **Recurring weekly** model: one **teacher**, one **student**, one **class slot**, **ISO weekday** `day_of_week` (1 = Monday … 7 = Sunday), **effective start_date**, optional **end_date**, **status**, **notes**.
- Full internal CRUD: index with filters (teacher, student, slot, weekday, status, text search), create, edit, show, destroy (**deactivate** active schedule).
- **Internal reschedule foundation:** edits update assignments and date range; **teacher changes** log an explicit activity event (`schedule.teacher_reassigned`).

### Schedule change log (`schedule_change_logs`)
- Append-only rows per schedule: **action** (e.g. `created`, `updated`, `deactivated`), **user_id**, **properties** (JSON, includes before/after snapshots on update).
- Shown on schedule **detail** page.

### Conflict prevention (`ScheduleConflictService`)
- For **active** schedules only, blocks overlapping date ranges where:
  - same **teacher** + same **slot** + same **weekday**, or
  - same **student** + same **slot** + same **weekday**.
- Overlap rule: `start_date` / `end_date` ranges intersect; open-ended schedules use an effective far-future end for comparison.
- **Inactive** teacher/student/slot cannot be used on **active** schedules (validated in Form Requests).
- Conflict checks are **skipped** when saving an **inactive** schedule (e.g. historical or disabled rows).

### Replacement / availability foundation
- `ScheduleConflictService::isTeacherBusy` / `isTeacherFree` for a given **calendar date** + slot + weekday (for future replacement UX).
- Schedules remain editable so supervisors can **reassign teachers** without a separate workflow in this phase.

### Integration
- **Teacher** and **student** **show** pages list linked schedules (with links to schedule detail).

### Activity logging
- Spatie **LogsActivity** on `ClassSlot` and `ClassSchedule`.
- Manual `activity()` on slot deactivate (when schedules still exist), schedule deactivate, and teacher reassignment on schedules.

### Access control
- **Routes:** `class-slots` and `class-schedules` live under `auth` + `role:Admin|Supervisor` ( **HR cannot access** scheduling UI).
- **Policies:** `ClassSlotPolicy` uses permission **`slot.manage`**; `ClassSchedulePolicy` uses **`schedule.manage`**.
- **Seeder:** `slot.manage` added; **Admin** has all permissions; **Supervisor** has `slot.manage` + `schedule.manage`; **HR** has neither.

## Scheduling Design Approach

- **Not** a full calendar engine: one row = **one recurring weekly commitment** (academy-style).
- **Occurrences** (for attendance) can be derived later from weekday + date range + exceptions; not built in Phase 3.

## Migrations Added

- `2026_03_25_120000_create_class_slots_table`
- `2026_03_25_120001_create_class_schedules_table`
- `2026_03_25_120002_create_schedule_change_logs_table`

## Key New / Updated Files

- **Models:** `ClassSlot`, `ClassSchedule`, `ScheduleChangeLog`; `Teacher` / `Student` **classSchedules** relations.
- **Services:** `App\Services\ScheduleConflictService`
- **Support:** `App\Support\ScheduleChangeLogger`
- **Policies:** `ClassSlotPolicy`, `ClassSchedulePolicy`
- **Requests:** `Store/UpdateClassSlotRequest`, `Store/UpdateClassScheduleRequest`
- **Controllers:** `ClassSlotController`, `ClassScheduleController`
- **Views:** `resources/views/admin/class-slots/*`, `class-schedules/*`, partial `day-of-week-options`; updates to **sidebar**, **dashboard**, **teacher/student show**.
- **Routes:** `routes/admin.php` (scheduling group with `role:Admin|Supervisor`)
- **Seeder:** `RoleAndPermissionSeeder` (`slot.manage`)

## DataTables

- Used on **class slot** and **class schedule** index pages (same CDN pattern as Phase 2).

## Assumptions

- **Timezone:** slot times are **naive** (academy local time); per-user display can be layered later.
- **Weekday:** stored as **1–7 (ISO weekday)**.
- **30 minutes:** strictly enforced for slot length to match the one-to-one class model.

## Next Recommended Step

**Phase 4:** Employee attendance page (digits-only ID), teacher login/logout events, and per-class **student** attendance—using these schedules as the backbone for “what class should run when.”
