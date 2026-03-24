# Phase 2 Summary — Master Data & People Foundation

**Status:** Complete for Phase 2 scope (no scheduling, attendance, finance, or portals beyond internal CRUD).

## What Was Implemented

- **Teachers:** Full internal CRUD (list/create/edit/show), auto-generated public ID `TCH-####`, linked `User` with `Teacher` role, active/inactive aligned with `users.is_active`, deactivation via soft status change (not hard delete).
- **Students:** Same pattern with public ID `STD-####` and `Student` role; parent links visible on student detail.
- **Parents:** CRUD with many-to-many links to students (`parent_student` unique pair); duplicate mappings prevented at DB level; links shown on parent and student detail views.
- **HR / Supervisor users:** Admin-only listing and creation of users with `HR` or `Supervisor` roles (password set by admin).
- **Search & tables:** Server-side query filters (`q`, `status`) on list pages plus **jQuery DataTables 1.13** (CDN) for sort/search on loaded rows.
- **Auth:** Inactive users (`is_active = false`) cannot log in after successful password check.
- **RBAC:** Policies use `teacher.manage` / `student.manage` / `parent.manage` for mutating actions and `teacher.view` / `student.view` / `parent.view` for read-only access (Supervisor). Portal roles (Teacher, Student, Parent, etc.) are excluded from `/admin/*` by `role:Admin|HR|Supervisor` middleware.

## Unique ID Generation

- Table `identifier_sequences` holds rows `teacher` and `student` with `next_value`.
- `App\Services\UniquePublicIdService` allocates IDs inside a DB transaction with `lockForUpdate()` on the row, increments `next_value`, and formats **`TCH-%04d`** and **`STD-%04d`**.
- IDs are stored on `teachers.public_id` and `students.public_id` (unique indexes). No manual entry in forms.

## User Account & Password Strategy

- **Teachers, students, parents:** Initial password is **set by staff** on create (required, min 8, confirmed). Updates allow optional password change.
- **HR/Supervisor:** Same — admin sets password at creation.
- **Security:** Passwords hashed with Laravel’s default hasher; credentials must be communicated out-of-band (email/SMS is not implemented in Phase 2).
- **Inactive users:** `users.is_active` and profile `status` are set inactive together on “deactivation”; login is blocked for inactive users.

## Access Control Decisions

| Role        | `/admin` area | Teachers / Students / Parents                         | HR/Supervisor user management |
|------------|---------------|--------------------------------------------------------|-------------------------------|
| **Admin**  | Yes           | Full manage (CRUD + deactivate)                       | Yes (create/list)           |
| **HR**     | Yes           | Manage teachers, students, parents (seeded permissions)| No                            |
| **Supervisor** | Yes       | **View only** (list/show; no create/edit/delete)      | No                            |
| **Others** | No            | —                                                      | —                             |

Routes under `routes/admin.php` use `auth` + `role:Admin|HR|Supervisor`. Staff routes are further restricted with `role:Admin`.

## Migrations Added (Phase 2)

- `identifier_sequences`
- `users.is_active`
- `teachers`, `students`, `parents`, `parent_student`

## Key Application Files

- **Controllers:** `App\Http\Controllers\Admin\{Teacher,Student,AcademyParent,StaffUser}Controller`, `AdminDashboardController`
- **Requests:** `App\Http\Requests\Admin\Store*`, `Update*` for teacher, student, parent, staff
- **Policies:** `TeacherPolicy`, `StudentPolicy`, `AcademyParentPolicy`
- **Service:** `App\Services\UniquePublicIdService`
- **Models:** `Teacher`, `Student`, `AcademyParent`, `User` (relations + `is_active`)
- **Views:** `resources/views/admin/{teachers,students,parents,staff}/**`, updated `layouts/app.blade.php` (`@stack('scripts')`), `sidebar`, `topbar`

## DataTables

- CDN: jQuery 3.7 + DataTables 1.13.8 on index pages for teachers, students, parents, and staff.
- Column sorting disabled on the Actions column where present.

## Activity Logging

- Spatie activity log on `Teacher`, `Student`, `AcademyParent` models (fillable changes).
- Explicit `activity()` entries for teacher/student/parent deactivation and parent–student sync; staff user creation; auth events unchanged from Phase 1.

## Next Recommended Step

**Phase 3:** Class scheduling, slots, and operational workflows (still out of scope for Phase 2), building on these master records and public IDs.
