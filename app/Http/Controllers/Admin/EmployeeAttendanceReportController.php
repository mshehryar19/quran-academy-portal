<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmployeeAttendanceEventsExport;
use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendanceEvent;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeAttendanceReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:reports.view', 'role:Admin|Supervisor|HR']);
    }

    public function index(Request $request): View
    {
        $query = $this->filteredEventsQuery($request)
            ->with(['teacher', 'classSession.classSchedule.classSlot'])
            ->orderByDesc('occurred_at');

        $events = $query->paginate(40)->withQueryString();
        $teachers = Teacher::query()->orderBy('full_name')->get();

        $unpairedLoginsQuery = EmployeeAttendanceEvent::query()
            ->with(['teacher', 'classSession'])
            ->where('event_type', 'login')
            ->whereDoesntHave('pairedLogout')
            ->orderByDesc('occurred_at');

        if ($request->filled('teacher_id')) {
            $unpairedLoginsQuery->where('teacher_id', $request->integer('teacher_id'));
        }
        if ($request->filled('from')) {
            $unpairedLoginsQuery->whereDate('attendance_date', '>=', $request->string('from')->toString());
        }
        if ($request->filled('to')) {
            $unpairedLoginsQuery->whereDate('attendance_date', '<=', $request->string('to')->toString());
        }

        $unpairedLogins = $unpairedLoginsQuery->limit(50)->get();

        return view('admin.reports.employee-attendance', compact('events', 'teachers', 'unpairedLogins'));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $this->logExport($request, 'excel');

        return Excel::download(
            new EmployeeAttendanceEventsExport(
                $request->filled('teacher_id') ? $request->integer('teacher_id') : null,
                $request->filled('from') ? $request->string('from')->toString() : null,
                $request->filled('to') ? $request->string('to')->toString() : null,
                $request->filled('event_type') ? $request->string('event_type')->toString() : null,
            ),
            'employee-attendance.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $this->logExport($request, 'pdf');

        /** @var Collection<int, EmployeeAttendanceEvent> $events */
        $events = $this->filteredEventsQuery($request)
            ->with(['teacher'])
            ->orderByDesc('occurred_at')
            ->limit(400)
            ->get();

        $pdf = Pdf::loadView('reports.pdf.employee-attendance', compact('events'));

        return $pdf->download('employee-attendance.pdf');
    }

    private function filteredEventsQuery(Request $request)
    {
        $query = EmployeeAttendanceEvent::query();

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->integer('teacher_id'));
        }
        if ($request->filled('from')) {
            $query->whereDate('attendance_date', '>=', $request->string('from')->toString());
        }
        if ($request->filled('to')) {
            $query->whereDate('attendance_date', '<=', $request->string('to')->toString());
        }
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->string('event_type'));
        }

        return $query;
    }

    private function logExport(Request $request, string $format): void
    {
        activity()
            ->causedBy($request->user())
            ->event('report.export')
            ->withProperties([
                'report' => 'employee_attendance',
                'format' => $format,
                'filters' => $request->only(['teacher_id', 'from', 'to', 'event_type']),
            ])
            ->log('Employee attendance report exported');
    }
}
