<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClassSessionsReportExport;
use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AcademicSessionReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:reports.view', 'role:Admin|Supervisor|HR']);
    }

    public function index(Request $request): View
    {
        $query = ClassSession::query()
            ->with([
                'classSchedule.teacher',
                'classSchedule.student',
                'classSchedule.classSlot',
                'studentAttendance',
                'lessonSummary',
            ])
            ->orderByDesc('session_date');

        if ($request->filled('teacher_id')) {
            $query->whereHas('classSchedule', fn ($q) => $q->where('teacher_id', $request->integer('teacher_id')));
        }
        if ($request->filled('from')) {
            $query->whereDate('session_date', '>=', $request->string('from')->toString());
        }
        if ($request->filled('to')) {
            $query->whereDate('session_date', '<=', $request->string('to')->toString());
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $sessions = $query->paginate(30)->withQueryString();
        $teachers = Teacher::query()->orderBy('full_name')->get();

        return view('admin.reports.class-sessions', compact('sessions', 'teachers'));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $this->logExport($request, 'excel');

        return Excel::download(
            new ClassSessionsReportExport(
                $request->filled('teacher_id') ? $request->integer('teacher_id') : null,
                $request->filled('from') ? $request->string('from')->toString() : null,
                $request->filled('to') ? $request->string('to')->toString() : null,
                $request->filled('status') ? $request->string('status')->toString() : null,
            ),
            'class-sessions.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $this->logExport($request, 'pdf');

        $query = ClassSession::query()
            ->with(['classSchedule.teacher', 'classSchedule.student'])
            ->orderByDesc('session_date');

        if ($request->filled('teacher_id')) {
            $query->whereHas('classSchedule', fn ($q) => $q->where('teacher_id', $request->integer('teacher_id')));
        }
        if ($request->filled('from')) {
            $query->whereDate('session_date', '>=', $request->string('from')->toString());
        }
        if ($request->filled('to')) {
            $query->whereDate('session_date', '<=', $request->string('to')->toString());
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        /** @var Collection<int, ClassSession> $sessions */
        $sessions = $query->limit(300)->get();

        $pdf = Pdf::loadView('reports.pdf.class-sessions', compact('sessions'));

        return $pdf->download('class-sessions.pdf');
    }

    private function logExport(Request $request, string $format): void
    {
        activity()
            ->causedBy($request->user())
            ->event('report.export')
            ->withProperties([
                'report' => 'class_sessions',
                'format' => $format,
                'filters' => $request->only(['teacher_id', 'from', 'to', 'status']),
            ])
            ->log('Class sessions report exported');
    }
}
