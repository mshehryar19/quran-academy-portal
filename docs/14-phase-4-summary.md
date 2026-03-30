# Phase 4 Summary — Academic Operations Foundation

**Status:** Complete for Phase 4 scope (no leave, salary, finance, notifications, full parent/student portals, reports export engine, violation module).

## What Was Implemented

### 1. Separate employee attendance page (kiosk)

- **Routes:** `routes/attendance.php` — `/attendance` identify → session-bound panel (no full portal login required for the kiosk flow).
- **Flow:** Teacher enters **digits-only** `attendance_digits` (6–16 digits); system resolves an **active** teacher with a linked **active** user; stores `attendance_teacher_id` in session; panel lists **today’s** synced `class_sessions` for sign-in selection; **sign-out** pairs with the oldest **unpaired** login for that teacher/date (FIFO via `EmployeeAttendancePairingService`).
- **Separation:** Not part of role dashboard navigation as primary workflow; linked from main dashboard copy for teachers. Admin configures digits on the teacher record (`StoreTeacherRequest` / `UpdateTeacherRequest`).

### 2. Attendance ID strategy

- Column **`teachers.attendance_digits`** — nullable, **unique**, validated `regex:/^\d{6,16}$/`; non-digits stripped on save in `TeacherController`.
- **Identification:** Lookup by exact digits + active teacher + active user; invalid combination shows a generic error (no user enumeration).

### 3. Employee attendance events & late detection

- Table **`employee_attendance_events`:** `teacher_id`, optional `class_session_id`, `event_type` (`login` / `logout`), `occurred_at`, `attendance_date`, `late_minutes` (nullable), `paired_login_event_id` on logout rows.
- **Late rule:** `LateAttendanceCalculator::lateMinutesAfterSlotStart` — compare clock time to **slot start** for the linked session; **0–15 minutes** after start → treated as on time (`late_minutes` null); **16+** → full minutes after start stored (e.g. 16 late → 16).
- **Incomplete attendance:** **`pairedLogout`** relationship on login rows; admin report lists **unpaired logins** (did not logout yet). Cross-checking “did not login” vs scheduled sessions is noted for a future reports module.

### 4. Class session / occurrence model

- Table **`class_sessions`:** `class_schedule_id`, `session_date`, `status` (`scheduled` / `completed`), unique `(class_schedule_id, session_date)`.
- **`ClassSessionService::syncSessionsForTeacherAndDate`** creates missing rows for **active** schedules whose weekday matches the given **calendar date**.
- Per-session data: **student attendance**, **lesson summary**, **homework**, **progress notes** attach to `class_session_id`.

### 5. Teacher portal — schedule & session workflow

- **`/my-classes`** (`routes/teacher.php`, `role:Teacher`): index by date; session **show** with forms for student attendance, lesson summary, homework, progress notes.
- **Authorization:** `ClassSessionPolicy` — teacher only for own schedule’s sessions; Admin/Supervisor broader view where applicable.

### 6. Student attendance (per class session)

- Table **`student_class_attendances`:** one row per `class_session_id` (`updateOrCreate`), `status` present/absent, `marked_by_user_id`, `marked_at`, **`teacher_available_for_reassignment`** (only meaningful when absent).

### 7. Lesson summaries

- Table **`lesson_summaries`:** SRS fields (topic, surah/lesson, memorization, performance, homework text), `submitted_at`, **`locked_at`** set on create.
- **Immutability:** Teachers cannot edit after submit; enforced by single-row-per-session + no teacher update route.
- **Submission window:** `StoreLessonSummaryRequest` — same calendar day or **next** calendar day end-of-day (`session_date->addDay()->endOfDay()`).

### 8. Admin-only override foundation

- Table **`lesson_summary_overrides`:** `previous_values` / `new_values` JSON, `admin_user_id`, optional `reason`.
- **`LessonSummaryPolicy`:** `override` **Admin only**; Supervisors can **view** summaries.
- **`LessonSummaryAdminController`:** `PUT` correction + Spatie `activity()` event `lesson_summary.admin_override`.

### 9. Homework / tasks

- Table **`homework_tasks`:** links to session (and optional `lesson_summary_id`), title, description, dates, `status` pending / completed / not_completed, `completion_marked_at`.
- Teacher marks completion via **PATCH**; `activity()` on create and completion update.

### 10. Basic progress foundation

- Table **`student_progress_notes`:** lightweight `body`, optional `class_session_id`, teacher/student linkage; `activity()` on create.

### 11. Admin / supervisor visibility

- **`EmployeeAttendanceReportController`** — filtered event list + **unpaired logins** block.
- **`AcademicSessionReportController`** — session list with link to lesson summary when present.

### 12. Activity logging (Phase 4)

- Employee attendance sign-in/out (`KioskController`), student attendance marked, lesson summary submitted, admin override, homework created/completion, progress note created.

## Access Control Summary

| Area | Admin | Supervisor | Teacher | HR |
|------|-------|------------|---------|-----|
| Scheduling CRUD | Yes | Yes | No | No |
| Employee attendance report | Yes | Yes | No | No |
| Class sessions report | Yes | Yes | No | No |
| Lesson summary view | Yes | Yes | Own | No |
| Lesson summary override | Yes | No | No | No |
| My classes / session ops | No | No | Own | No |
| Attendance kiosk | Any user with URL (identify by digits); **assignment of digits is admin-controlled** | | | |

## Migrations (representative)

- `add_attendance_digits_to_teachers_table`
- `create_class_sessions_table`
- `create_employee_attendance_events_table`
- `create_student_class_attendances_table`
- `create_lesson_summaries_table`
- `create_lesson_summary_overrides_table`
- `create_homework_tasks_table`
- `create_student_progress_notes_table`

## Key Files

- **Models:** `ClassSession`, `EmployeeAttendanceEvent`, `StudentClassAttendance`, `LessonSummary`, `LessonSummaryOverride`, `HomeworkTask`, `StudentProgressNote`
- **Services:** `LateAttendanceCalculator`, `ClassSessionService`, `EmployeeAttendancePairingService`
- **Policies:** `ClassSessionPolicy`, `LessonSummaryPolicy`
- **Controllers:** `Attendance\KioskController`, `Teacher\MyScheduleController`, `Teacher\ClassSessionController`, `Admin\EmployeeAttendanceReportController`, `Admin\AcademicSessionReportController`, `Admin\LessonSummaryAdminController`
- **Views:** `resources/views/attendance/*`, `teacher/schedule/index`, `teacher/sessions/show`, `admin/reports/*`, `admin/lesson-summaries/show`

## Assumptions

- **Timezone:** Same as Phase 3 — slot times are local/academy time.
- **Kiosk security:** Session-based after identify; suitable for controlled office tablets; not a public internet kiosk without network/session hardening.
- **Reassignment:** Availability flag only; full supervisor reassignment engine deferred.

## Next Recommended Step

**Phase 5 / later:** Leave management, salary linkage to attendance data, parent visibility on homework completion, notifications, and structured reports (including scheduled-vs-login reconciliation for “did not login”).
