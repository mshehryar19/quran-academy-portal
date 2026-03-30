<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentAttendanceReportExport;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentClassAttendance;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentAttendanceReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:reports.view', 'role:Admin|Supervisor|HR']);
    }

    public function index(Request $request)
    {
        $query = StudentClassAttendance::query()
            ->with([
                'classSession.classSchedule.teacher',
                'classSession.classSchedule.student',
                'markedBy',
            ])
            ->orderByDesc('marked_at');

        $query->whereHas('classSession', function ($q) use ($request): void {
            if ($request->filled('from')) {
                $q->whereDate('session_date', '>=', $request->string('from')->toString());
            }
            if ($request->filled('to')) {
                $q->whereDate('session_date', '<=', $request->string('to')->toString());
            }
            if ($request->filled('teacher_id')) {
                $q->whereHas('classSchedule', fn ($cs) => $cs->where('teacher_id', $request->integer('teacher_id')));
            }
            if ($request->filled('student_id')) {
                $q->whereHas('classSchedule', fn ($cs) => $cs->where('student_id', $request->integer('student_id')));
            }
        });

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $rows = $query->paginate(40)->withQueryString();
        $teachers = Teacher::query()->orderBy('full_name')->get();
        $students = Student::query()->orderBy('full_name')->get();

        return view('admin.reports.student-attendance', compact('rows', 'teachers', 'students'));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $this->logExport($request, 'excel');

        return Excel::download(
            new StudentAttendanceReportExport(
                $request->filled('teacher_id') ? $request->integer('teacher_id') : null,
                $request->filled('student_id') ? $request->integer('student_id') : null,
                $request->filled('from') ? $request->string('from')->toString() : null,
                $request->filled('to') ? $request->string('to')->toString() : null,
                $request->filled('status') ? $request->string('status')->toString() : null,
            ),
            'student-class-attendance.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $this->logExport($request, 'pdf');

        $query = StudentClassAttendance::query()
            ->with([
                'classSession.classSchedule.teacher',
                'classSession.classSchedule.student',
            ])
            ->orderByDesc('marked_at');

        $query->whereHas('classSession', function ($q) use ($request): void {
            if ($request->filled('from')) {
                $q->whereDate('session_date', '>=', $request->string('from')->toString());
            }
            if ($request->filled('to')) {
                $q->whereDate('session_date', '<=', $request->string('to')->toString());
            }
            if ($request->filled('teacher_id')) {
                $q->whereHas('classSchedule', fn ($cs) => $cs->where('teacher_id', $request->integer('teacher_id')));
            }
            if ($request->filled('student_id')) {
                $q->whereHas('classSchedule', fn ($cs) => $cs->where('student_id', $request->integer('student_id')));
            }
        });

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        /** @var Collection<int, StudentClassAttendance> $rows */
        $rows = $query->limit(400)->get();

        $pdf = Pdf::loadView('reports.pdf.student-attendance', ['rows' => $rows]);

        return $pdf->download('student-class-attendance.pdf');
    }

    private function logExport(Request $request, string $format): void
    {
        activity()
            ->causedBy($request->user())
            ->event('report.export')
            ->withProperties([
                'report' => 'student_attendance',
                'format' => $format,
                'filters' => $request->only(['teacher_id', 'student_id', 'from', 'to', 'status']),
            ])
            ->log('Student attendance report exported');
    }
}
