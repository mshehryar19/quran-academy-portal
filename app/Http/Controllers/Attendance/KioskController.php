<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\EmployeeAttendanceEvent;
use App\Models\Teacher;
use App\Services\ClassSessionService;
use App\Services\EmployeeAttendancePairingService;
use App\Services\LateAttendanceCalculator;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KioskController extends Controller
{
    public function identify(): View
    {
        return view('attendance.identify');
    }

    public function establish(Request $request): RedirectResponse
    {
        $digits = preg_replace('/\D/', '', (string) $request->input('attendance_digits', ''));

        $request->merge(['attendance_digits' => $digits]);

        $validated = $request->validate([
            'attendance_digits' => ['required', 'regex:/^\d{6,16}$/'],
        ]);

        $teacher = Teacher::query()
            ->where('attendance_digits', $validated['attendance_digits'])
            ->where('status', 'active')
            ->with('user')
            ->first();

        if (! $teacher || ! $teacher->user?->is_active) {
            return back()->withErrors([
                'attendance_digits' => __('Invalid or inactive attendance ID.'),
            ])->withInput();
        }

        $request->session()->put('attendance_teacher_id', $teacher->id);

        return redirect()->route('attendance.panel');
    }

    public function panel(Request $request, ClassSessionService $sessionService): View
    {
        $teacher = Teacher::query()->findOrFail($request->session()->get('attendance_teacher_id'));
        $date = Carbon::today();
        $sessions = $sessionService->syncSessionsForTeacherAndDate($teacher, $date);
        $openLogin = app(EmployeeAttendancePairingService::class)->findUnpairedLogin($teacher, $date);

        return view('attendance.panel', compact('teacher', 'sessions', 'date', 'openLogin'));
    }

    public function signIn(Request $request): RedirectResponse
    {
        $teacher = Teacher::query()->findOrFail($request->session()->get('attendance_teacher_id'));

        $validated = $request->validate([
            'class_session_id' => ['required', 'exists:class_sessions,id'],
        ]);

        $classSession = ClassSession::query()->with('classSchedule')->findOrFail($validated['class_session_id']);

        if ((int) $classSession->classSchedule->teacher_id !== (int) $teacher->id) {
            abort(403);
        }

        $now = now();
        $slotStart = $classSession->slotStartsAt();
        $late = app(LateAttendanceCalculator::class)->lateMinutesAfterSlotStart($slotStart, $now);

        EmployeeAttendanceEvent::query()->create([
            'teacher_id' => $teacher->id,
            'class_session_id' => $classSession->id,
            'event_type' => 'login',
            'occurred_at' => $now,
            'attendance_date' => $now->toDateString(),
            'late_minutes' => $late > 0 ? $late : null,
        ]);

        activity()
            ->causedBy($teacher->user)
            ->event('employee_attendance.login')
            ->withProperties([
                'class_session_id' => $classSession->id,
                'late_minutes' => $late,
            ])
            ->log('Employee attendance sign-in');

        return back()->with('status', __('Sign-in recorded.'));
    }

    public function signOut(Request $request): RedirectResponse
    {
        $teacher = Teacher::query()->findOrFail($request->session()->get('attendance_teacher_id'));

        $pairing = app(EmployeeAttendancePairingService::class);
        $login = $pairing->findUnpairedLogin($teacher, Carbon::today());

        if (! $login) {
            return back()->withErrors([
                'signout' => __('No open sign-in found for today.'),
            ]);
        }

        $now = now();

        EmployeeAttendanceEvent::query()->create([
            'teacher_id' => $teacher->id,
            'class_session_id' => $login->class_session_id,
            'event_type' => 'logout',
            'occurred_at' => $now,
            'attendance_date' => $now->toDateString(),
            'paired_login_event_id' => $login->id,
        ]);

        activity()
            ->causedBy($teacher->user)
            ->event('employee_attendance.logout')
            ->log('Employee attendance sign-out');

        return back()->with('status', __('Sign-out recorded.'));
    }

    public function leave(Request $request): RedirectResponse
    {
        $request->session()->forget('attendance_teacher_id');

        return redirect()->route('attendance.identify')->with('status', __('Attendance session ended.'));
    }
}
